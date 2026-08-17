// Cascarón del panel: barra lateral oscura tipo cockpit + contenido.
import { NavLink, useNavigate } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { Building2, CalendarClock, CarFront, LayoutDashboard, Layers, LogOut, Users } from "lucide-react";
import type { ReactNode } from "react";
import type { Session } from "./api/types";
import { logout, getMyCompany } from "./api/endpoints";
import { imageUrl } from "./api/client";

const linkCls = ({ isActive }: { isActive: boolean }) =>
  `flex items-center gap-2.5 rounded-md px-3 py-2 text-sm font-semibold transition-colors ${
    isActive ? "bg-caribe-500/20 text-caribe-400" : "text-ink-100/70 hover:bg-ink-800 hover:text-ink-100"
  }`;

export default function Shell({ session, children }: { session: Session; children: ReactNode }) {
  const navigate = useNavigate();
  const { data: company } = useQuery({
    queryKey: ["myCompany"],
    queryFn: () => getMyCompany(),
    enabled: !session.isSuper,
    staleTime: 5 * 60 * 1000,
  });

  const doLogout = async () => {
    await logout();
    navigate("/login");
  };

  const logo = imageUrl(company?.logo);

  return (
    <div className="flex min-h-[100dvh]">
      <aside className="fixed inset-y-0 left-0 z-40 flex w-56 flex-col bg-ink-950 text-ink-100">
        <div className="flex items-center gap-2.5 border-b border-ink-800 px-4 py-4">
          {!session.isSuper && logo ? (
            <img src={logo} alt="" className="size-8 rounded-md bg-white object-contain p-0.5" />
          ) : (
            <div className="grid size-8 place-items-center rounded-md bg-mango-500 font-display text-sm font-extrabold text-ink-950">
              {session.isSuper ? "RD" : (company?.name?.[0] ?? "·")}
            </div>
          )}
          <div className="min-w-0">
            <p className="truncate font-display text-sm font-bold leading-tight" data-testid="text-brand">
              {session.isSuper ? "Plataforma RentRD" : (company?.name ?? session.companySlug ?? "Mi empresa")}
            </p>
            <p className="text-[11px] text-ink-100/50">{session.isSuper ? "Super administración" : "Panel de empresa"}</p>
          </div>
        </div>

        <nav className="flex-1 space-y-1 overflow-y-auto px-3 py-4">
          {session.isSuper ? (
            <NavLink to="/companies" className={linkCls} data-testid="link-companies">
              <Building2 className="size-4" /> Empresas
            </NavLink>
          ) : (
            <>
              <NavLink to="/" end className={linkCls} data-testid="link-dashboard">
                <LayoutDashboard className="size-4" /> Resumen
              </NavLink>
              <NavLink to="/fleet" className={linkCls} data-testid="link-fleet">
                <CarFront className="size-4" /> Flota
              </NavLink>
              <NavLink to="/bookings" className={linkCls} data-testid="link-bookings">
                <CalendarClock className="size-4" /> Reservas
              </NavLink>
              <NavLink to="/catalogs" className={linkCls} data-testid="link-catalogs">
                <Layers className="size-4" /> Catálogos
              </NavLink>
              <NavLink to="/staff" className={linkCls} data-testid="link-staff">
                <Users className="size-4" /> Personal
              </NavLink>
            </>
          )}
        </nav>

        <div className="border-t border-ink-800 px-3 py-3">
          <div className="mb-2 px-2">
            <p className="truncate text-sm font-semibold" data-testid="text-user-name">
              {session.user ? `${session.user.name} ${session.user.lastname}`.trim() : "Super Admin"}
            </p>
            <p className="text-[11px] text-ink-100/50">
              {session.isSuper ? "Plataforma" : session.user?.kind === 1 ? "Administrador" : "Empleado"}
            </p>
          </div>
          <button onClick={doLogout} className="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-sm text-ink-100/70 transition-colors hover:bg-ink-800 hover:text-ink-100" data-testid="button-logout">
            <LogOut className="size-4" /> Cerrar sesión
          </button>
        </div>
      </aside>

      <main className="ml-56 flex-1 px-8 py-6">{children}</main>
    </div>
  );
}
