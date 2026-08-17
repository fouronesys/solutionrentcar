// Flota: vehículos con galería, precio por día y atributos de catálogo.
import { useEffect, useState, type FormEvent } from "react";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { CarFront, ImagePlus, Plus, Star, Trash2, X } from "lucide-react";
import {
  createCar, deleteCar, getMyCompany, listCars, listCatalog, setCarImages, updateCar, type CarInput,
} from "../api/endpoints";
import { fileToBase64, imageUrl } from "../api/client";
import { fmtMoney } from "../lib";
import { Badge, btnPrimary, btnSecondary, Confirm, EmptyState, ErrorBox, Field, inputCls, Modal, PageHeader, Spinner } from "../ui";
import type { Car } from "../api/types";

export default function Fleet() {
  const q = useQuery({ queryKey: ["cars"], queryFn: () => listCars() });
  const company = useQuery({ queryKey: ["myCompany"], queryFn: () => getMyCompany(), staleTime: 5 * 60 * 1000 });
  const qc = useQueryClient();
  const currency = company.data?.currency ?? "DOP";

  const [editing, setEditing] = useState<Car | null | "new">(null);
  const [deleting, setDeleting] = useState<Car | null>(null);

  const remove = useMutation({
    mutationFn: (id: number) => deleteCar(id),
    onSuccess: () => { setDeleting(null); qc.invalidateQueries({ queryKey: ["cars"] }); },
  });

  return (
    <div>
      <PageHeader title="Flota" sub="Cada vehículo, su precio por día y su disponibilidad."
        action={<button className={btnPrimary} onClick={() => setEditing("new")} data-testid="button-new-car"><Plus className="size-4" /> Nuevo vehículo</button>} />

      {q.isLoading && <Spinner label="Cargando flota…" />}
      {q.error != null && <ErrorBox error={q.error} onRetry={() => q.refetch()} />}

      {q.data && q.data.length === 0 && (
        <EmptyState icon={<CarFront className="size-6" />} title="Tu flota está esperando su primer vehículo"
          hint="Registra el primer carro con sus fotos, precio por día y atributos. Aparecerá al instante en tu app."
          action={<button className={btnPrimary} onClick={() => setEditing("new")}><Plus className="size-4" /> Registrar vehículo</button>} />
      )}

      {q.data && q.data.length > 0 && (
        <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
          {q.data.map((car) => {
            const img = imageUrl(car.image ?? car.images?.[0]);
            return (
              <div key={car.id} className="rise group overflow-hidden rounded-xl border border-line bg-card transition-shadow hover:shadow-lg" data-testid={`card-car-${car.id}`}>
                <div className="relative h-40 bg-ink-100">
                  {img ? (
                    <img src={img} alt={car.name} className="size-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
                  ) : (
                    <div className="grid size-full place-items-center text-ink-600/40"><CarFront className="size-10" /></div>
                  )}
                  <div className="absolute left-2 top-2">
                    <Badge tone={car.status === 0 ? "ok" : "warn"}>{car.status === 0 ? "Disponible" : "Ocupado"}</Badge>
                  </div>
                </div>
                <div className="p-4">
                  <div className="flex items-start justify-between gap-2">
                    <div>
                      <h3 className="font-display font-bold text-ink-950">{car.name}</h3>
                      <p className="text-xs text-ink-600">
                        {[car.brand, car.year, car.plate].filter(Boolean).join(" · ") || "Sin detalles"}
                      </p>
                    </div>
                    <p className="whitespace-nowrap font-mono text-sm font-bold text-caribe-600">{fmtMoney(car.price, currency)}<span className="text-[10px] text-ink-600">/día</span></p>
                  </div>
                  <div className="mt-2 flex flex-wrap gap-1 text-[11px] text-ink-600">
                    {[car.category, car.transmission, car.fuel].filter(Boolean).map((t) => (
                      <span key={t} className="rounded-full bg-paper px-2 py-0.5 font-semibold">{t}</span>
                    ))}
                  </div>
                  <div className="mt-3 flex justify-end gap-1.5 border-t border-line pt-3">
                    <button className={btnSecondary + " !px-2.5 !py-1 text-xs"} onClick={() => setEditing(car)} data-testid={`button-edit-car-${car.id}`}>Editar</button>
                    <button className="rounded-md p-1.5 text-ink-600 transition-colors hover:bg-bad-100 hover:text-bad-600" onClick={() => setDeleting(car)} data-testid={`button-delete-car-${car.id}`}>
                      <Trash2 className="size-4" />
                    </button>
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      )}

      {editing !== null && <CarModal car={editing === "new" ? null : editing} onClose={() => setEditing(null)} />}
      {deleting && (
        <Confirm title={`Eliminar ${deleting.name}`}
          message="El vehículo se eliminará de tu flota y de la app. Si tiene reservas activas la API lo impedirá."
          confirmLabel="Eliminar vehículo" pending={remove.isPending} error={remove.error}
          onConfirm={() => remove.mutate(deleting.id)} onClose={() => { setDeleting(null); remove.reset(); }} />
      )}
    </div>
  );
}

function useCat(kind: "brands" | "categories" | "transmissions" | "fuels" | "stocks") {
  return useQuery({ queryKey: ["catalog", kind], queryFn: () => listCatalog(kind) });
}

function CarModal({ car, onClose }: { car: Car | null; onClose: () => void }) {
  const qc = useQueryClient();
  const brands = useCat("brands");
  const categories = useCat("categories");
  const transmissions = useCat("transmissions");
  const fuels = useCat("fuels");
  const stocks = useCat("stocks");

  const [form, setForm] = useState<CarInput>({
    name: car?.name ?? "", year: car?.year ?? "", plate: car?.plate ?? "", seat: car?.seat ?? "",
    kms: car?.kms ?? "", description: car?.description ?? "", price: car?.price ?? 0,
    status: car?.status ?? 0, brand_id: car?.brand_id, category_id: car?.category_id,
    transmission_id: car?.transmission_id, fuel_id: car?.fuel_id, stock_id: car?.stock_id,
  });
  // Galería: URLs existentes + nuevas base64; la primera es la principal.
  const [images, setImages] = useState<string[]>([]);
  useEffect(() => {
    if (car?.images?.length) setImages(car.images);
    else if (car?.image) setImages([car.image]);
  }, [car]);

  const save = useMutation({
    mutationFn: async () => {
      const body: CarInput = { ...form, price: Number(form.price) };
      const saved = car ? await updateCar(car.id, body) : await createCar(body);
      if (images.length > 0 || (car && (car.images?.length ?? 0) > 0)) {
        await setCarImages(saved.id, images);
      }
      return saved;
    },
    onSuccess: () => { qc.invalidateQueries({ queryKey: ["cars"] }); onClose(); },
  });

  const addFiles = async (files: FileList | null) => {
    if (!files) return;
    const arr = await Promise.all(Array.from(files).map(fileToBase64));
    setImages((s) => [...s, ...arr]);
  };

  const setNum = (k: keyof CarInput) => (e: React.ChangeEvent<HTMLSelectElement>) =>
    setForm((s) => ({ ...s, [k]: e.target.value ? Number(e.target.value) : undefined }));
  const setStr = (k: keyof CarInput) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) =>
    setForm((s) => ({ ...s, [k]: e.target.value }));

  const submit = (e: FormEvent) => { e.preventDefault(); save.mutate(); };

  const catSelect = (
    label: string, key: keyof CarInput, q2: ReturnType<typeof useCat>, testid: string,
  ) => (
    <Field label={label}>
      <select className={inputCls} value={(form[key] as number | undefined) ?? ""} onChange={setNum(key)} data-testid={testid}>
        <option value="">— Sin asignar —</option>
        {(q2.data ?? []).map((i) => <option key={i.id} value={i.id}>{i.name}</option>)}
      </select>
    </Field>
  );

  return (
    <Modal title={car ? `Editar ${car.name}` : "Nuevo vehículo"} onClose={onClose} wide>
      <form onSubmit={submit} className="space-y-4">
        <div className="grid gap-3 sm:grid-cols-3">
          <div className="sm:col-span-2">
            <Field label="Nombre del vehículo">
              <input className={inputCls} value={form.name ?? ""} onChange={setStr("name")} required placeholder="Toyota Corolla SE 2023" data-testid="input-car-name" />
            </Field>
          </div>
          <Field label="Precio por día">
            <input className={inputCls + " font-mono"} type="number" min={0} step="0.01" value={form.price ?? 0}
              onChange={(e) => setForm((s) => ({ ...s, price: Number(e.target.value) }))} required data-testid="input-car-price" />
          </Field>
        </div>

        <div className="grid gap-3 sm:grid-cols-4">
          <Field label="Año"><input className={inputCls} value={form.year ?? ""} onChange={setStr("year")} placeholder="2023" data-testid="input-car-year" /></Field>
          <Field label="Placa"><input className={inputCls} value={form.plate ?? ""} onChange={setStr("plate")} placeholder="A123456" data-testid="input-car-plate" /></Field>
          <Field label="Asientos"><input className={inputCls} value={form.seat ?? ""} onChange={setStr("seat")} placeholder="5" data-testid="input-car-seat" /></Field>
          <Field label="Kilometraje"><input className={inputCls} value={form.kms ?? ""} onChange={setStr("kms")} placeholder="45200" data-testid="input-car-kms" /></Field>
        </div>

        <div className="grid gap-3 sm:grid-cols-3">
          {catSelect("Marca", "brand_id", brands, "select-car-brand")}
          {catSelect("Categoría", "category_id", categories, "select-car-category")}
          {catSelect("Transmisión", "transmission_id", transmissions, "select-car-transmission")}
        </div>
        <div className="grid gap-3 sm:grid-cols-3">
          {catSelect("Combustible", "fuel_id", fuels, "select-car-fuel")}
          {catSelect("Sucursal", "stock_id", stocks, "select-car-stock")}
          <Field label="Disponibilidad">
            <select className={inputCls} value={form.status} onChange={(e) => setForm((s) => ({ ...s, status: Number(e.target.value) }))} data-testid="select-car-status">
              <option value={0}>Disponible</option>
              <option value={1}>Ocupado (reservado/rentado)</option>
            </select>
          </Field>
        </div>

        <Field label="Descripción">
          <textarea className={inputCls + " min-h-16 resize-y"} value={form.description ?? ""} onChange={setStr("description")}
            placeholder="Detalles que el cliente verá en la app…" data-testid="input-car-description" />
        </Field>

        <div>
          <p className="mb-1.5 text-sm font-semibold text-ink-800">Fotos <span className="font-normal text-ink-600">(la primera es la principal)</span></p>
          <div className="flex flex-wrap gap-2">
            {images.map((img, i) => (
              <div key={i} className="group/img relative size-20 overflow-hidden rounded-lg border border-line bg-white">
                <img src={img.startsWith("data:") ? img : (imageUrl(img) ?? img)} alt="" className="size-full object-cover" />
                {i === 0 && (
                  <span className="absolute left-1 top-1 grid size-5 place-items-center rounded-full bg-mango-500 text-ink-950"><Star className="size-3" /></span>
                )}
                <div className="absolute inset-0 hidden items-center justify-center gap-1 bg-ink-950/60 group-hover/img:flex">
                  {i !== 0 && (
                    <button type="button" title="Hacer principal" className="rounded bg-white/90 p-1 text-ink-950 hover:bg-mango-400 transition-colors"
                      onClick={() => setImages((s) => [s[i], ...s.filter((_, j) => j !== i)])} data-testid={`button-main-image-${i}`}>
                      <Star className="size-3.5" />
                    </button>
                  )}
                  <button type="button" title="Quitar" className="rounded bg-white/90 p-1 text-bad-600 hover:bg-bad-100 transition-colors"
                    onClick={() => setImages((s) => s.filter((_, j) => j !== i))} data-testid={`button-remove-image-${i}`}>
                    <X className="size-3.5" />
                  </button>
                </div>
              </div>
            ))}
            <label className="grid size-20 cursor-pointer place-items-center rounded-lg border border-dashed border-line text-ink-600 transition-colors hover:border-caribe-500 hover:text-caribe-600" data-testid="button-add-images">
              <ImagePlus className="size-5" />
              <input type="file" accept="image/*" multiple className="hidden" onChange={(e) => addFiles(e.target.files)} />
            </label>
          </div>
        </div>

        {save.error != null && <ErrorBox error={save.error} />}
        <div className="flex justify-end gap-2 border-t border-line pt-4">
          <button type="button" className={btnSecondary} onClick={onClose}>Cancelar</button>
          <button type="submit" className={btnPrimary} disabled={save.isPending} data-testid="button-save-car">
            {save.isPending ? "Guardando…" : car ? "Guardar cambios" : "Registrar vehículo"}
          </button>
        </div>
      </form>
    </Modal>
  );
}
