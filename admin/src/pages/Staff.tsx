// Personal del panel de la empresa.
import { useState, type FormEvent } from "react";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { Plus, Users } from "lucide-react";
import { createStaff, listStaff, updateStaff, type StaffInput } from "../api/endpoints";
import type { StaffUser } from "../api/types";
import { Badge, btnPrimary, btnSecondary, EmptyState, ErrorBox, Field, inputCls, Modal, PageHeader, Spinner } from "../ui";

export default function Staff() {
  const q = useQuery({ queryKey: ["staff"], queryFn: () => listStaff() });
  const qc = useQueryClient();
  const [editing, setEditing] = useState<StaffUser | null | "new">(null);

  const toggle = useMutation({
    mutationFn: (u: StaffUser) => updateStaff(u.id, { status: u.status === 1 ? 0 : 1 }),
    onSuccess: () => qc.invalidateQueries({ queryKey: ["staff"] }),
  });

  return (
    <div>
      <PageHeader title="Personal" sub="Quién puede entrar a este panel y con qué permisos."
        action={<button className={btnPrimary} onClick={() => setEditing("new")} data-testid="button-new-staff"><Plus className="size-4" /> Nuevo usuario</button>} />

      {q.isLoading && <Spinner label="Cargando personal…" />}
      {q.error != null && <ErrorBox error={q.error} onRetry={() => q.refetch()} />}
      {toggle.error != null && <div className="mb-3"><ErrorBox error={toggle.error} /></div>}

      {q.data && q.data.length === 0 && (
        <EmptyState icon={<Users className="size-6" />} title="Todavía trabajas en solitario"
          hint="Crea cuentas para tu equipo: administradores con control total o empleados para la operación diaria."
          action={<button className={btnPrimary} onClick={() => setEditing("new")}><Plus className="size-4" /> Crear primer usuario</button>} />
      )}

      {q.data && q.data.length > 0 && (
        <div className="overflow-hidden rounded-xl border border-line bg-card">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-line bg-paper text-left text-xs font-bold uppercase tracking-wide text-ink-600">
                <th className="px-4 py-2.5">Nombre</th>
                <th className="px-4 py-2.5">Usuario</th>
                <th className="px-4 py-2.5">Contacto</th>
                <th className="px-4 py-2.5">Rol</th>
                <th className="px-4 py-2.5">Estado</th>
                <th className="px-4 py-2.5"></th>
              </tr>
            </thead>
            <tbody>
              {q.data.map((u) => (
                <tr key={u.id} className="border-b border-line/60 last:border-0 transition-colors hover:bg-caribe-50/60" data-testid={`row-staff-${u.id}`}>
                  <td className="px-4 py-2.5">
                    <div className="flex items-center gap-2.5">
                      <div className="grid size-8 place-items-center rounded-full bg-caribe-100 font-display text-xs font-bold text-caribe-600">
                        {(u.name?.[0] ?? "") + (u.lastname?.[0] ?? "")}
                      </div>
                      <span className="font-semibold">{u.name} {u.lastname}</span>
                    </div>
                  </td>
                  <td className="px-4 py-2.5 font-mono text-xs text-ink-600">{u.username ?? "—"}</td>
                  <td className="px-4 py-2.5 text-ink-600">
                    <div className="text-xs">{u.email || "—"}</div>
                    <div className="text-xs">{u.phone || ""}</div>
                  </td>
                  <td className="px-4 py-2.5"><Badge tone={u.kind === 1 ? "info" : "muted"}>{u.kind === 1 ? "Administrador" : "Empleado"}</Badge></td>
                  <td className="px-4 py-2.5"><Badge tone={u.status === 1 ? "ok" : "bad"}>{u.status === 1 ? "Activo" : "Inactivo"}</Badge></td>
                  <td className="px-4 py-2.5 text-right whitespace-nowrap">
                    <button className="rounded-md px-2 py-1 text-xs font-bold text-caribe-600 transition-colors hover:bg-caribe-100"
                      onClick={() => setEditing(u)} data-testid={`button-edit-staff-${u.id}`}>Editar</button>
                    <button className={`rounded-md px-2 py-1 text-xs font-bold transition-colors ${u.status === 1 ? "text-bad-600 hover:bg-bad-100" : "text-ok-600 hover:bg-ok-100"}`}
                      onClick={() => toggle.mutate(u)} disabled={toggle.isPending} data-testid={`button-toggle-staff-${u.id}`}>
                      {u.status === 1 ? "Desactivar" : "Activar"}
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {editing !== null && <StaffModal user={editing === "new" ? null : editing} onClose={() => setEditing(null)} />}
    </div>
  );
}

function StaffModal({ user, onClose }: { user: StaffUser | null; onClose: () => void }) {
  const qc = useQueryClient();
  const [form, setForm] = useState<StaffInput>({
    username: user?.username ?? "", password: "", name: user?.name ?? "", lastname: user?.lastname ?? "",
    email: user?.email ?? "", phone: user?.phone ?? "", kind: user?.kind ?? 0, status: user?.status ?? 1,
  });

  const save = useMutation({
    mutationFn: () => {
      const body: StaffInput = { ...form };
      if (!body.password) delete body.password;
      if (user) { delete body.username; return updateStaff(user.id, body); }
      return createStaff(body);
    },
    onSuccess: () => { qc.invalidateQueries({ queryKey: ["staff"] }); onClose(); },
  });

  const set = (k: keyof StaffInput) => (e: React.ChangeEvent<HTMLInputElement>) =>
    setForm((s) => ({ ...s, [k]: e.target.value }));

  const submit = (e: FormEvent) => { e.preventDefault(); save.mutate(); };

  return (
    <Modal title={user ? `Editar a ${user.name}` : "Nuevo usuario del panel"} onClose={onClose}>
      <form onSubmit={submit} className="space-y-3">
        <div className="grid gap-3 sm:grid-cols-2">
          <Field label="Nombre">
            <input className={inputCls} value={form.name ?? ""} onChange={set("name")} required data-testid="input-staff-name" />
          </Field>
          <Field label="Apellido">
            <input className={inputCls} value={form.lastname ?? ""} onChange={set("lastname")} data-testid="input-staff-lastname" />
          </Field>
        </div>
        <div className="grid gap-3 sm:grid-cols-2">
          <Field label="Email">
            <input className={inputCls} type="email" value={form.email ?? ""} onChange={set("email")} data-testid="input-staff-email" />
          </Field>
          <Field label="Teléfono">
            <input className={inputCls} value={form.phone ?? ""} onChange={set("phone")} data-testid="input-staff-phone" />
          </Field>
        </div>
        <div className="grid gap-3 sm:grid-cols-2">
          <Field label="Usuario" hint={user ? "El usuario no se puede cambiar." : undefined}>
            <input className={inputCls + (user ? " opacity-60" : "")} value={form.username ?? ""} onChange={set("username")} disabled={!!user} required={!user} autoComplete="off" data-testid="input-staff-username" />
          </Field>
          <Field label={user ? "Nueva contraseña" : "Contraseña"} hint={user ? "Déjala vacía para no cambiarla." : undefined}>
            <input className={inputCls} type="password" value={form.password ?? ""} onChange={set("password")} required={!user} minLength={6} autoComplete="new-password" data-testid="input-staff-password" />
          </Field>
        </div>
        <div className="grid gap-3 sm:grid-cols-2">
          <Field label="Rol">
            <select className={inputCls} value={form.kind} onChange={(e) => setForm((s) => ({ ...s, kind: Number(e.target.value) }))} data-testid="select-staff-kind">
              <option value={0}>Empleado</option>
              <option value={1}>Administrador</option>
            </select>
          </Field>
          <Field label="Estado">
            <select className={inputCls} value={form.status} onChange={(e) => setForm((s) => ({ ...s, status: Number(e.target.value) }))} data-testid="select-staff-status">
              <option value={1}>Activo</option>
              <option value={0}>Inactivo</option>
            </select>
          </Field>
        </div>
        {save.error != null && <ErrorBox error={save.error} />}
        <div className="flex justify-end gap-2 pt-1">
          <button type="button" className={btnSecondary} onClick={onClose}>Cancelar</button>
          <button type="submit" className={btnPrimary} disabled={save.isPending} data-testid="button-save-staff">
            {save.isPending ? "Guardando…" : user ? "Guardar cambios" : "Crear usuario"}
          </button>
        </div>
      </form>
    </Modal>
  );
}
