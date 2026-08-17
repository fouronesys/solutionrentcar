// Reservas: lista filtrable + detalle con acciones de entrega/devolución/cancelación.
import { useState, type FormEvent } from "react";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { CalendarClock, KeySquare, PenLine, Undo2, XCircle } from "lucide-react";
import {
  BOOKING_STATUS, cancelBooking, deliverBooking, getBooking, getMyCompany, listBookings, returnBooking,
} from "../api/endpoints";
import { imageUrl } from "../api/client";
import { fmtDate, fmtMoney } from "../lib";
import { Badge, bookingBadgeTone, btnDanger, btnPrimary, btnSecondary, EmptyState, ErrorBox, Field, inputCls, Modal, PageHeader, Spinner } from "../ui";

const FILTERS: { label: string; status?: number }[] = [
  { label: "Todas" },
  { label: "Reservadas", status: 0 },
  { label: "Firmadas", status: 1 },
  { label: "Entregadas", status: 3 },
  { label: "Devueltas", status: 4 },
  { label: "Canceladas", status: 2 },
];

export default function Bookings() {
  const [filter, setFilter] = useState<number | undefined>(undefined);
  const [openId, setOpenId] = useState<number | null>(null);
  const company = useQuery({ queryKey: ["myCompany"], queryFn: () => getMyCompany(), staleTime: 5 * 60 * 1000 });
  const currency = company.data?.currency ?? "DOP";

  const q = useQuery({
    queryKey: ["bookings", filter ?? "all"],
    queryFn: () => listBookings({ status: filter, limit: 100 }),
  });

  return (
    <div>
      <PageHeader title="Reservas" sub="La operación del día: entregas, devoluciones y cancelaciones." />

      <div className="mb-4 flex flex-wrap gap-1.5">
        {FILTERS.map((f) => (
          <button key={f.label} onClick={() => setFilter(f.status)}
            className={`rounded-full px-3.5 py-1.5 text-sm font-bold transition-colors ${
              f.status === filter ? "bg-ink-950 text-white" : "bg-card border border-line text-ink-800 hover:border-caribe-500 hover:text-caribe-600"
            }`}
            data-testid={`tab-bookings-${f.label.toLowerCase()}`}>
            {f.label}
          </button>
        ))}
      </div>

      {q.isLoading && <Spinner label="Cargando reservas…" />}
      {q.error != null && <ErrorBox error={q.error} onRetry={() => q.refetch()} />}

      {q.data && q.data.length === 0 && (
        <EmptyState icon={<CalendarClock className="size-6" />} title="Nada por aquí"
          hint={filter === undefined ? "Cuando lleguen reservas desde la app, este será tu centro de mando." : "No hay reservas con este estado en este momento."} />
      )}

      {q.data && q.data.length > 0 && (
        <div className="overflow-hidden rounded-xl border border-line bg-card">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-line bg-paper text-left text-xs font-bold uppercase tracking-wide text-ink-600">
                <th className="px-4 py-2.5">Código</th>
                <th className="px-4 py-2.5">Cliente</th>
                <th className="px-4 py-2.5">Vehículo</th>
                <th className="px-4 py-2.5">Periodo</th>
                <th className="px-4 py-2.5 text-right">Total</th>
                <th className="px-4 py-2.5">Estado</th>
              </tr>
            </thead>
            <tbody>
              {q.data.map((b) => (
                <tr key={b.id} onClick={() => setOpenId(b.id)}
                  className="cursor-pointer border-b border-line/60 last:border-0 transition-colors hover:bg-caribe-50/60"
                  data-testid={`row-booking-${b.id}`}>
                  <td className="px-4 py-2.5 font-mono text-xs font-bold text-caribe-600">{b.code ?? `#${b.id}`}</td>
                  <td className="px-4 py-2.5 font-semibold">{b.client_name || "—"}</td>
                  <td className="px-4 py-2.5 text-ink-800">{b.car_name || `Vehículo #${b.car_id}`}</td>
                  <td className="px-4 py-2.5 text-ink-600">{fmtDate(b.start_at)} → {fmtDate(b.end_at)}</td>
                  <td className="px-4 py-2.5 text-right font-mono">{fmtMoney(b.total, currency)}</td>
                  <td className="px-4 py-2.5"><Badge tone={bookingBadgeTone(b.status)}>{BOOKING_STATUS[b.status] ?? b.status}</Badge></td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {openId !== null && <BookingDetail id={openId} currency={currency} onClose={() => setOpenId(null)} />}
    </div>
  );
}

function Row({ k, v }: { k: string; v: React.ReactNode }) {
  return (
    <div className="flex justify-between gap-4 border-b border-line/60 py-1.5 text-sm last:border-0">
      <span className="text-ink-600">{k}</span>
      <span className="text-right font-semibold text-ink-950">{v}</span>
    </div>
  );
}

function BookingDetail({ id, currency, onClose }: { id: number; currency: string; onClose: () => void }) {
  const qc = useQueryClient();
  const q = useQuery({ queryKey: ["booking", id], queryFn: () => getBooking(id) });
  const [action, setAction] = useState<"deliver" | "return" | null>(null);
  const [fuel, setFuel] = useState("");
  const [kms, setKms] = useState("");
  const [comment, setComment] = useState("");

  const done = () => {
    qc.invalidateQueries({ queryKey: ["bookings"] });
    qc.invalidateQueries({ queryKey: ["booking", id] });
    setAction(null); setFuel(""); setKms(""); setComment("");
  };

  const mDeliver = useMutation({ mutationFn: () => deliverBooking(id, { fuel: fuel || undefined, kms: kms || undefined, comment: comment || undefined }), onSuccess: done });
  const mReturn = useMutation({ mutationFn: () => returnBooking(id, { fuel: fuel || undefined, kms: kms || undefined, comment: comment || undefined }), onSuccess: done });
  const mCancel = useMutation({ mutationFn: () => cancelBooking(id), onSuccess: done });

  const b = q.data;
  const carImg = imageUrl(b?.car?.image ?? b?.car?.images?.[0]);
  const sig = imageUrl(b?.signature);

  const submitAction = (e: FormEvent) => {
    e.preventDefault();
    if (action === "deliver") mDeliver.mutate();
    else if (action === "return") mReturn.mutate();
  };

  return (
    <Modal title={b ? `Reserva ${b.code ?? `#${b.id}`}` : "Reserva"} onClose={onClose} wide>
      {q.isLoading && <Spinner />}
      {q.error != null && <ErrorBox error={q.error} onRetry={() => q.refetch()} />}
      {b && (
        <div className="grid gap-5 sm:grid-cols-2">
          <div>
            <div className="mb-3 flex items-center justify-between">
              <h3 className="font-display text-sm font-bold uppercase tracking-wide text-ink-600">Detalle</h3>
              <Badge tone={bookingBadgeTone(b.status)}>{BOOKING_STATUS[b.status] ?? b.status}</Badge>
            </div>
            <Row k="Cliente" v={b.client ? `${b.client.name} ${b.client.lastname}` : "—"} />
            <Row k="Teléfono" v={b.client?.phone || "—"} />
            <Row k="Inicio" v={fmtDate(b.start_at)} />
            <Row k="Fin" v={fmtDate(b.end_at)} />
            <Row k="Días" v={b.day ?? "—"} />
            <Row k="Precio/día" v={fmtMoney(b.price, currency)} />
            <Row k="Total" v={<span className="font-mono">{fmtMoney(b.total, currency)}</span>} />
            <Row k="Lugar de entrega" v={b.place_start || "—"} />
            <Row k="Lugar de devolución" v={b.place_end || "—"} />
            {b.comment && <Row k="Comentario" v={b.comment} />}
          </div>

          <div>
            <h3 className="mb-3 font-display text-sm font-bold uppercase tracking-wide text-ink-600">Vehículo</h3>
            <div className="overflow-hidden rounded-lg border border-line">
              {carImg && <img src={carImg} alt="" className="h-32 w-full object-cover" />}
              <div className="p-3">
                <p className="font-display font-bold text-ink-950" data-testid="text-booking-car">{b.car?.name ?? `Vehículo #${b.car_id}`}</p>
                <p className="text-xs text-ink-600">{[b.car?.brand, b.car?.year, b.car?.plate].filter(Boolean).join(" · ")}</p>
              </div>
            </div>

            {sig && (
              <div className="mt-4">
                <h3 className="mb-2 flex items-center gap-1.5 font-display text-sm font-bold uppercase tracking-wide text-ink-600">
                  <PenLine className="size-3.5" /> Firma del cliente
                </h3>
                <img src={sig} alt="Firma del cliente" className="h-24 w-full rounded-lg border border-line bg-white object-contain p-2" data-testid="img-signature" />
              </div>
            )}
          </div>

          <div className="sm:col-span-2 border-t border-line pt-4">
            {(mDeliver.error ?? mReturn.error ?? mCancel.error) != null && (
              <div className="mb-3"><ErrorBox error={mDeliver.error ?? mReturn.error ?? mCancel.error} /></div>
            )}

            {action ? (
              <form onSubmit={submitAction} className="rise space-y-3 rounded-lg bg-paper p-4">
                <p className="font-display text-sm font-bold text-ink-950">
                  {action === "deliver" ? "Registrar entrega del vehículo" : "Registrar devolución del vehículo"}
                </p>
                <div className="grid gap-3 sm:grid-cols-2">
                  <Field label="Nivel de combustible">
                    <input className={inputCls} value={fuel} onChange={(e) => setFuel(e.target.value)} placeholder="3/4" data-testid="input-action-fuel" />
                  </Field>
                  <Field label="Kilometraje">
                    <input className={inputCls} value={kms} onChange={(e) => setKms(e.target.value)} placeholder="45320" data-testid="input-action-kms" />
                  </Field>
                </div>
                <Field label="Comentario">
                  <input className={inputCls} value={comment} onChange={(e) => setComment(e.target.value)} placeholder="Observaciones del estado del vehículo…" data-testid="input-action-comment" />
                </Field>
                <div className="flex justify-end gap-2">
                  <button type="button" className={btnSecondary} onClick={() => setAction(null)}>Volver</button>
                  <button type="submit" className={btnPrimary} disabled={mDeliver.isPending || mReturn.isPending} data-testid="button-confirm-action">
                    {mDeliver.isPending || mReturn.isPending ? "Registrando…" : action === "deliver" ? "Confirmar entrega" : "Confirmar devolución"}
                  </button>
                </div>
              </form>
            ) : (
              <div className="flex flex-wrap justify-end gap-2">
                {(b.status === 0 || b.status === 1) && (
                  <>
                    <button className={btnDanger} onClick={() => mCancel.mutate()} disabled={mCancel.isPending} data-testid="button-cancel-booking">
                      <XCircle className="size-4" /> {mCancel.isPending ? "Cancelando…" : "Cancelar reserva"}
                    </button>
                    <button className={btnPrimary} onClick={() => setAction("deliver")} data-testid="button-deliver-booking">
                      <KeySquare className="size-4" /> Entregar vehículo
                    </button>
                  </>
                )}
                {b.status === 3 && (
                  <button className={btnPrimary} onClick={() => setAction("return")} data-testid="button-return-booking">
                    <Undo2 className="size-4" /> Registrar devolución
                  </button>
                )}
                {(b.status === 2 || b.status === 4) && (
                  <p className="text-sm text-ink-600">Esta reserva ya está {b.status === 4 ? "devuelta" : "cancelada"}; no requiere más acciones.</p>
                )}
              </div>
            )}
          </div>
        </div>
      )}
    </Modal>
  );
}
