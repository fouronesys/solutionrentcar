// Resumen operativo del día — calculado en cliente con flota + reservas.
import { useQuery } from "@tanstack/react-query";
import { Link } from "react-router-dom";
import { ArrowUpRight, CalendarClock, CarFront, CheckCircle2, KeySquare } from "lucide-react";
import { listBookings, listCars, getMyCompany, BOOKING_STATUS } from "../api/endpoints";
import { fmtDate, fmtMoney } from "../lib";
import { Badge, bookingBadgeTone, EmptyState, ErrorBox, PageHeader, Spinner } from "../ui";

function Stat({ icon, label, value, accent }: { icon: React.ReactNode; label: string; value: string | number; accent?: boolean }) {
  return (
    <div className={`rise rounded-xl border px-4 py-3.5 ${accent ? "border-mango-500/40 bg-mango-50" : "border-line bg-card"}`}>
      <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-ink-600">
        {icon} {label}
      </div>
      <p className="mt-1.5 font-mono text-2xl font-bold text-ink-950" data-testid={`stat-${label.toLowerCase().replace(/\s+/g, "-")}`}>{value}</p>
    </div>
  );
}

export default function Dashboard() {
  const cars = useQuery({ queryKey: ["cars"], queryFn: () => listCars() });
  const bookings = useQuery({ queryKey: ["bookings", "all"], queryFn: () => listBookings({ limit: 100 }) });
  const company = useQuery({ queryKey: ["myCompany"], queryFn: () => getMyCompany(), staleTime: 5 * 60 * 1000 });

  if (cars.isLoading || bookings.isLoading) return <Spinner label="Preparando el resumen del día…" />;
  if (cars.error) return <ErrorBox error={cars.error} onRetry={() => cars.refetch()} />;
  if (bookings.error) return <ErrorBox error={bookings.error} onRetry={() => bookings.refetch()} />;

  const fleet = cars.data ?? [];
  const bks = bookings.data ?? [];
  const currency = company.data?.currency ?? "DOP";
  const available = fleet.filter((c) => c.status === 0).length;
  const reserved = bks.filter((b) => b.status === 0 || b.status === 1).length;
  const delivered = bks.filter((b) => b.status === 3).length;
  const returned = bks.filter((b) => b.status === 4).length;
  const recent = [...bks].sort((a, b) => b.id - a.id).slice(0, 8);

  const hoy = new Intl.DateTimeFormat("es-DO", { weekday: "long", day: "numeric", month: "long" }).format(new Date());

  return (
    <div>
      <PageHeader title="Resumen operativo" sub={`Hoy es ${hoy}. Esto es lo que está pasando en tu operación.`} />

      <div className="grid grid-cols-2 gap-3 lg:grid-cols-5">
        <Stat icon={<CarFront className="size-3.5" />} label="Flota total" value={fleet.length} />
        <Stat icon={<CheckCircle2 className="size-3.5" />} label="Disponibles" value={available} accent />
        <Stat icon={<CalendarClock className="size-3.5" />} label="Reservadas" value={reserved} />
        <Stat icon={<KeySquare className="size-3.5" />} label="En calle" value={delivered} />
        <Stat icon={<CheckCircle2 className="size-3.5" />} label="Devueltas" value={returned} />
      </div>

      <div className="mt-6 flex items-center justify-between">
        <h2 className="font-display text-lg font-bold text-ink-950">Reservas recientes</h2>
        <Link to="/bookings" className="inline-flex items-center gap-1 text-sm font-semibold text-caribe-600 hover:underline" data-testid="link-all-bookings">
          Ver todas <ArrowUpRight className="size-3.5" />
        </Link>
      </div>

      {recent.length === 0 ? (
        <div className="mt-3">
          <EmptyState icon={<CalendarClock className="size-6" />} title="Sin reservas todavía"
            hint="Cuando tus clientes reserven desde la app, aparecerán aquí en tiempo real." />
        </div>
      ) : (
        <div className="mt-3 overflow-hidden rounded-xl border border-line bg-card">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-line bg-paper text-left text-xs font-bold uppercase tracking-wide text-ink-600">
                <th className="px-4 py-2.5">Código</th>
                <th className="px-4 py-2.5">Cliente</th>
                <th className="px-4 py-2.5">Vehículo</th>
                <th className="px-4 py-2.5">Inicio</th>
                <th className="px-4 py-2.5 text-right">Total</th>
                <th className="px-4 py-2.5">Estado</th>
              </tr>
            </thead>
            <tbody>
              {recent.map((b) => (
                <tr key={b.id} className="border-b border-line/60 last:border-0 transition-colors hover:bg-caribe-50/60" data-testid={`row-booking-${b.id}`}>
                  <td className="px-4 py-2.5 font-mono text-xs font-bold text-caribe-600">{b.code ?? `#${b.id}`}</td>
                  <td className="px-4 py-2.5 font-semibold">{b.client_name || "—"}</td>
                  <td className="px-4 py-2.5 text-ink-800">{b.car_name || `Vehículo #${b.car_id}`}</td>
                  <td className="px-4 py-2.5 text-ink-600">{fmtDate(b.start_at)}</td>
                  <td className="px-4 py-2.5 text-right font-mono">{fmtMoney(b.total, currency)}</td>
                  <td className="px-4 py-2.5"><Badge tone={bookingBadgeTone(b.status)}>{BOOKING_STATUS[b.status] ?? b.status}</Badge></td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
