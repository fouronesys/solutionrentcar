// Crear/editar empresa con vista previa en vivo de la app móvil.
import { useEffect, useRef, useState, type FormEvent } from "react";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { Link, useNavigate, useParams } from "react-router-dom";
import { ArrowLeft, ImagePlus, Loader2, Save, Smartphone, Trash2, UserPlus } from "lucide-react";
import {
  createCompany, createStaff, deleteCompany, getCompany, updateCompany, uploadCompanyLogo, type CompanyInput,
} from "../api/endpoints";
import { fileToBase64, imageUrl } from "../api/client";
import { errMsg } from "../lib";
import { btnDanger, btnPrimary, btnSecondary, ErrorBox, Field, inputCls, Modal, Spinner } from "../ui";

const empty: Required<Omit<CompanyInput, "logo">> = {
  slug: "", name: "", color_primary: "#1193ad", color_secondary: "#f59211",
  currency: "DOP", phone: "", email: "", address: "", active: true,
};

function PhonePreview({ name, logo, primary, secondary, currency }: {
  name: string; logo: string | null; primary: string; secondary: string; currency: string;
}) {
  return (
    <div className="sticky top-6">
      <p className="mb-2 flex items-center gap-1.5 text-xs font-bold uppercase tracking-wide text-ink-600">
        <Smartphone className="size-3.5" /> Vista previa de la app
      </p>
      <div className="mx-auto w-[240px] rounded-[2rem] border-[6px] border-ink-950 bg-white shadow-2xl">
        <div className="overflow-hidden rounded-[1.6rem]">
          <div className="px-4 pb-5 pt-6 text-white" style={{ background: primary }}>
            <div className="mx-auto mb-2 grid size-14 place-items-center overflow-hidden rounded-xl bg-white/95">
              {logo ? (
                <img src={logo} alt="" className="size-full object-contain p-1" />
              ) : (
                <span className="font-display text-lg font-extrabold" style={{ color: primary }}>{name ? name[0].toUpperCase() : "?"}</span>
              )}
            </div>
            <p className="text-center font-display text-sm font-bold leading-tight" data-testid="text-preview-name">{name || "Tu Rent-Car"}</p>
            <p className="mt-0.5 text-center text-[10px] opacity-75">Alquiler de vehículos</p>
          </div>
          <div className="space-y-2 p-3">
            {[["Toyota Corolla 2023", "2,850"], ["Hyundai Tucson 2024", "4,200"]].map(([n, p]) => (
              <div key={n} className="rounded-lg border border-line p-2">
                <div className="mb-1.5 h-14 rounded-md bg-paper" />
                <p className="text-[10px] font-bold text-ink-950">{n}</p>
                <div className="mt-1 flex items-center justify-between">
                  <span className="font-mono text-[10px] font-bold" style={{ color: primary }}>{currency} {p}/día</span>
                  <span className="rounded-full px-2 py-0.5 text-[9px] font-bold text-white" style={{ background: secondary }}>Reservar</span>
                </div>
              </div>
            ))}
            <div className="flex justify-around border-t border-line pt-2 text-[9px] font-semibold text-ink-600">
              <span style={{ color: primary }}>Inicio</span><span>Reservas</span><span>Perfil</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default function CompanyForm() {
  const { id } = useParams();
  const isNew = !id || id === "new";
  const companyId = isNew ? 0 : Number(id);
  const navigate = useNavigate();
  const qc = useQueryClient();

  const [form, setForm] = useState(empty);
  const [logoData, setLogoData] = useState<string | null>(null); // base64 nueva
  const [existingLogo, setExistingLogo] = useState<string | null>(null);
  const fileRef = useRef<HTMLInputElement>(null);
  const [showAdmin, setShowAdmin] = useState(false);

  const q = useQuery({
    queryKey: ["company", companyId],
    queryFn: () => getCompany(companyId),
    enabled: !isNew,
  });

  const initialized = useRef(false);
  useEffect(() => {
    if (q.data && !initialized.current) {
      initialized.current = true;
      const c = q.data;
      setForm({
        slug: c.slug, name: c.name, color_primary: c.color_primary || "#1193ad",
        color_secondary: c.color_secondary || "#f59211", currency: c.currency || "DOP",
        phone: c.phone || "", email: c.email || "", address: c.address || "", active: c.active,
      });
      setExistingLogo(imageUrl(c.logo));
    }
  }, [q.data]);

  const save = useMutation({
    mutationFn: async () => {
      if (isNew) {
        const input: CompanyInput = { ...form };
        if (logoData) input.logo = logoData;
        return createCompany(input);
      }
      const { slug: _slug, ...rest } = form;
      const updated = await updateCompany(companyId, rest);
      if (logoData) return uploadCompanyLogo(companyId, logoData);
      return updated;
    },
    onSuccess: (c) => {
      qc.invalidateQueries({ queryKey: ["companies"] });
      qc.setQueryData(["company", c.id], c);
      if (isNew) navigate(`/companies/${c.id}`);
    },
  });

  const pickLogo = async (f: File | undefined) => {
    if (!f) return;
    setLogoData(await fileToBase64(f));
  };

  const set = (k: keyof typeof empty) => (e: React.ChangeEvent<HTMLInputElement>) =>
    setForm((s) => ({ ...s, [k]: e.target.value }));

  if (!isNew && q.isLoading) return <Spinner label="Cargando empresa…" />;
  if (!isNew && q.error != null) return <ErrorBox error={q.error} onRetry={() => q.refetch()} />;

  const previewLogo = logoData ?? existingLogo;

  const submit = (e: FormEvent) => {
    e.preventDefault();
    save.mutate();
  };

  return (
    <div>
      <Link to="/companies" className="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-caribe-600 hover:underline" data-testid="link-back-companies">
        <ArrowLeft className="size-4" /> Empresas
      </Link>
      <div className="mb-5 flex flex-wrap items-end justify-between gap-3">
        <div>
          <h1 className="font-display text-2xl font-bold text-ink-950">{isNew ? "Nueva empresa" : `Editar: ${form.name || "…"}`}</h1>
          <p className="mt-0.5 text-sm text-ink-600">
            {isNew ? "Define la identidad de la rent-car; verás su app tomar forma a la derecha." : "Los cambios de marca se reflejan en la app de sus clientes."}
          </p>
        </div>
        {!isNew && (
          <button className={btnSecondary} onClick={() => setShowAdmin(true)} data-testid="button-create-admin">
            <UserPlus className="size-4" /> Crear usuario admin
          </button>
        )}
      </div>

      <div className="grid gap-8 lg:grid-cols-[1fr_280px]">
        <form onSubmit={submit} className="rise space-y-4 rounded-xl border border-line bg-card p-5">
          <div className="grid gap-4 sm:grid-cols-2">
            <Field label="Slug" hint={isNew ? "Identificador único, ej. caribe-motors. No se puede cambiar después." : "El slug no se puede modificar."}>
              <input className={inputCls + (isNew ? "" : " opacity-60")} value={form.slug} onChange={set("slug")} disabled={!isNew} required pattern="[a-z0-9\-]+" placeholder="caribe-motors" data-testid="input-slug" />
            </Field>
            <Field label="Nombre">
              <input className={inputCls} value={form.name} onChange={set("name")} required placeholder="Caribe Motors Rent-Car" data-testid="input-name" />
            </Field>
          </div>

          <Field label="Logo">
            <div className="flex items-center gap-3">
              <button type="button" className={btnSecondary} onClick={() => fileRef.current?.click()} data-testid="button-upload-logo">
                <ImagePlus className="size-4" /> {previewLogo ? "Cambiar logo" : "Subir logo"}
              </button>
              {previewLogo && <img src={previewLogo} alt="Logo" className="size-10 rounded-md border border-line bg-white object-contain p-1" data-testid="img-logo-preview" />}
              <input ref={fileRef} type="file" accept="image/*" className="hidden" onChange={(e) => pickLogo(e.target.files?.[0])} />
            </div>
          </Field>

          <div className="grid gap-4 sm:grid-cols-3">
            <Field label="Color primario">
              <div className="flex items-center gap-2">
                <input type="color" value={form.color_primary} onChange={set("color_primary")} className="h-9 w-12 cursor-pointer rounded border border-line bg-white" data-testid="input-color-primary" />
                <input className={inputCls + " font-mono"} value={form.color_primary} onChange={set("color_primary")} />
              </div>
            </Field>
            <Field label="Color secundario">
              <div className="flex items-center gap-2">
                <input type="color" value={form.color_secondary} onChange={set("color_secondary")} className="h-9 w-12 cursor-pointer rounded border border-line bg-white" data-testid="input-color-secondary" />
                <input className={inputCls + " font-mono"} value={form.color_secondary} onChange={set("color_secondary")} />
              </div>
            </Field>
            <Field label="Moneda">
              <select className={inputCls} value={form.currency} onChange={(e) => setForm((s) => ({ ...s, currency: e.target.value }))} data-testid="select-currency">
                <option value="DOP">DOP — Peso dominicano</option>
                <option value="USD">USD — Dólar</option>
              </select>
            </Field>
          </div>

          <div className="grid gap-4 sm:grid-cols-2">
            <Field label="Teléfono">
              <input className={inputCls} value={form.phone} onChange={set("phone")} placeholder="809-555-0100" data-testid="input-phone" />
            </Field>
            <Field label="Email">
              <input className={inputCls} type="email" value={form.email} onChange={set("email")} placeholder="contacto@empresa.do" data-testid="input-email" />
            </Field>
          </div>
          <Field label="Dirección">
            <input className={inputCls} value={form.address} onChange={set("address")} placeholder="Av. Winston Churchill 45, Santo Domingo" data-testid="input-address" />
          </Field>

          <label className="flex cursor-pointer items-center gap-2 text-sm font-semibold text-ink-800">
            <input type="checkbox" checked={form.active} onChange={(e) => setForm((s) => ({ ...s, active: e.target.checked }))} className="size-4 accent-caribe-500" data-testid="checkbox-active" />
            Empresa activa en la plataforma
          </label>

          {save.error != null && <ErrorBox error={save.error} />}
          {save.isSuccess && !save.isPending && (
            <p className="rounded-md bg-ok-100 px-3 py-2 text-sm font-semibold text-ok-600" data-testid="text-saved">Cambios guardados correctamente.</p>
          )}

          <div className="flex justify-end gap-2 border-t border-line pt-4">
            <Link to="/companies" className={btnSecondary}>Cancelar</Link>
            <button type="submit" className={btnPrimary} disabled={save.isPending} data-testid="button-save-company">
              {save.isPending ? <Loader2 className="size-4 animate-spin" /> : <Save className="size-4" />}
              {isNew ? "Crear empresa" : "Guardar cambios"}
            </button>
          </div>
        </form>

        <PhonePreview name={form.name} logo={previewLogo} primary={form.color_primary} secondary={form.color_secondary} currency={form.currency} />
      </div>

      {!isNew && <DangerZone companyId={companyId} slug={form.slug} />}

      {showAdmin && <AdminModal companyId={companyId} onClose={() => setShowAdmin(false)} />}
    </div>
  );
}

/** Eliminación definitiva de la empresa: pide escribir el slug para confirmar. */
function DangerZone({ companyId, slug }: { companyId: number; slug: string }) {
  const navigate = useNavigate();
  const qc = useQueryClient();
  const [open, setOpen] = useState(false);
  const [confirm, setConfirm] = useState("");
  const del = useMutation({
    mutationFn: () => deleteCompany(companyId, confirm.trim()),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ["companies"] });
      navigate("/companies");
    },
  });

  return (
    <div className="mt-8 max-w-2xl rounded-xl border border-bad-600/40 bg-bad-100/40 p-4">
      <h2 className="font-display text-sm font-bold uppercase tracking-wide text-bad-600">Zona de peligro</h2>
      <p className="mt-1 text-sm text-ink-600">
        Eliminar la empresa borra de forma <strong>definitiva</strong> sus usuarios, clientes, flota, reservas,
        pagos y archivos. Si solo quieres retirarla temporalmente, desmarca "Empresa activa" y guarda.
      </p>
      {!open ? (
        <button type="button" className={btnDanger + " mt-3"} onClick={() => setOpen(true)} data-testid="button-open-delete-company">
          <Trash2 className="size-4" /> Eliminar empresa…
        </button>
      ) : (
        <div className="mt-3 space-y-3">
          <Field label={`Escribe el slug (${slug}) para confirmar`}>
            <input className={inputCls} value={confirm} onChange={(e) => setConfirm(e.target.value)} placeholder={slug} data-testid="input-confirm-slug" />
          </Field>
          {del.error != null && <ErrorBox error={del.error} />}
          <div className="flex gap-2">
            <button type="button" className={btnSecondary} onClick={() => { setOpen(false); setConfirm(""); }}>Cancelar</button>
            <button type="button" className={btnDanger} disabled={confirm.trim() !== slug || del.isPending}
              onClick={() => del.mutate()} data-testid="button-confirm-delete-company">
              {del.isPending ? <Loader2 className="size-4 animate-spin" /> : <Trash2 className="size-4" />}
              Eliminar definitivamente
            </button>
          </div>
        </div>
      )}
    </div>
  );
}

function AdminModal({ companyId, onClose }: { companyId: number; onClose: () => void }) {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [name, setName] = useState("");
  const [lastname, setLastname] = useState("");
  const [pending, setPending] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [done, setDone] = useState(false);

  const submit = async (e: FormEvent) => {
    e.preventDefault();
    setPending(true);
    setError(null);
    try {
      await createStaff({ username, password, name, lastname, kind: 1, status: 1 }, companyId);
      setDone(true);
    } catch (err) {
      setError(errMsg(err));
    } finally {
      setPending(false);
    }
  };

  return (
    <Modal title="Primer administrador de la empresa" onClose={onClose}>
      {done ? (
        <div className="text-center">
          <p className="font-semibold text-ok-600" data-testid="text-admin-created">Administrador creado. Ya puede entrar al panel con el slug de su empresa.</p>
          <button className={btnPrimary + " mt-4"} onClick={onClose}>Listo</button>
        </div>
      ) : (
        <form onSubmit={submit} className="space-y-3">
          <div className="grid gap-3 sm:grid-cols-2">
            <Field label="Nombre">
              <input className={inputCls} value={name} onChange={(e) => setName(e.target.value)} required data-testid="input-admin-name" />
            </Field>
            <Field label="Apellido">
              <input className={inputCls} value={lastname} onChange={(e) => setLastname(e.target.value)} data-testid="input-admin-lastname" />
            </Field>
          </div>
          <Field label="Usuario">
            <input className={inputCls} value={username} onChange={(e) => setUsername(e.target.value)} required autoComplete="off" data-testid="input-admin-username" />
          </Field>
          <Field label="Contraseña">
            <input className={inputCls} type="password" value={password} onChange={(e) => setPassword(e.target.value)} required minLength={6} autoComplete="new-password" data-testid="input-admin-password" />
          </Field>
          {error && <p className="rounded-md bg-bad-100 px-3 py-2 text-sm font-semibold text-bad-600">{error}</p>}
          <div className="flex justify-end gap-2 pt-1">
            <button type="button" className={btnSecondary} onClick={onClose}>Cancelar</button>
            <button type="submit" className={btnPrimary} disabled={pending} data-testid="button-save-admin">
              {pending ? "Creando…" : "Crear administrador"}
            </button>
          </div>
        </form>
      )}
    </Modal>
  );
}
