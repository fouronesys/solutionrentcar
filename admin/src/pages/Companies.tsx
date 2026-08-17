// Super admin — listado de empresas de la plataforma.
import { useQuery } from "@tanstack/react-query";
import { Link } from "react-router-dom";
import { Building2, Pencil, Plus } from "lucide-react";
import { listCompanies } from "../api/endpoints";
import { imageUrl } from "../api/client";
import { Badge, btnPrimary, EmptyState, ErrorBox, PageHeader, Spinner } from "../ui";

export default function Companies() {
  const q = useQuery({ queryKey: ["companies"], queryFn: listCompanies });

  return (
    <div>
      <PageHeader title="Empresas" sub="Todas las rent-car que operan sobre la plataforma."
        action={<Link to="/companies/new" className={btnPrimary} data-testid="button-new-company"><Plus className="size-4" /> Nueva empresa</Link>} />

      {q.isLoading && <Spinner label="Cargando empresas…" />}
      {q.error != null && <ErrorBox error={q.error} onRetry={() => q.refetch()} />}

      {q.data && q.data.length === 0 && (
        <EmptyState icon={<Building2 className="size-6" />} title="La plataforma está lista para su primera empresa"
          hint="Da de alta la primera rent-car: define su marca, colores y su primer administrador."
          action={<Link to="/companies/new" className={btnPrimary}><Plus className="size-4" /> Crear la primera empresa</Link>} />
      )}

      {q.data && q.data.length > 0 && (
        <div className="overflow-hidden rounded-xl border border-line bg-card">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-line bg-paper text-left text-xs font-bold uppercase tracking-wide text-ink-600">
                <th className="px-4 py-2.5">Empresa</th>
                <th className="px-4 py-2.5">Slug</th>
                <th className="px-4 py-2.5">Moneda</th>
                <th className="px-4 py-2.5">Contacto</th>
                <th className="px-4 py-2.5">Marca</th>
                <th className="px-4 py-2.5">Estado</th>
                <th className="px-4 py-2.5"></th>
              </tr>
            </thead>
            <tbody>
              {q.data.map((c) => {
                const logo = imageUrl(c.logo);
                return (
                  <tr key={c.id} className="border-b border-line/60 last:border-0 transition-colors hover:bg-caribe-50/60" data-testid={`row-company-${c.id}`}>
                    <td className="px-4 py-2.5">
                      <div className="flex items-center gap-2.5">
                        {logo ? (
                          <img src={logo} alt="" className="size-8 rounded-md border border-line bg-white object-contain p-0.5" />
                        ) : (
                          <div className="grid size-8 place-items-center rounded-md font-display text-xs font-bold text-white" style={{ background: c.color_primary || "#1193ad" }}>
                            {c.name[0]}
                          </div>
                        )}
                        <span className="font-semibold text-ink-950">{c.name}</span>
                      </div>
                    </td>
                    <td className="px-4 py-2.5 font-mono text-xs text-caribe-600">{c.slug}</td>
                    <td className="px-4 py-2.5 font-mono text-xs">{c.currency}</td>
                    <td className="px-4 py-2.5 text-ink-600">
                      <div>{c.phone || "—"}</div>
                      <div className="text-xs">{c.email || ""}</div>
                    </td>
                    <td className="px-4 py-2.5">
                      <div className="flex gap-1">
                        <span className="size-4 rounded-full border border-line" style={{ background: c.color_primary }} title={c.color_primary} />
                        <span className="size-4 rounded-full border border-line" style={{ background: c.color_secondary }} title={c.color_secondary} />
                      </div>
                    </td>
                    <td className="px-4 py-2.5">
                      <Badge tone={c.active ? "ok" : "bad"}>{c.active ? "Activa" : "Inactiva"}</Badge>
                    </td>
                    <td className="px-4 py-2.5 text-right">
                      <Link to={`/companies/${c.id}`} className="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-bold text-caribe-600 transition-colors hover:bg-caribe-100" data-testid={`link-edit-company-${c.id}`}>
                        <Pencil className="size-3.5" /> Editar
                      </Link>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
