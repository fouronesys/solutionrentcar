// Primitivas de UI del panel: densas, consistentes, con estados cuidados.
import { useEffect, type ReactNode } from "react";
import { AlertTriangle, Loader2, X } from "lucide-react";

export function Spinner({ label = "Cargando…" }: { label?: string }) {
  return (
    <div className="flex items-center gap-2 py-10 justify-center text-ink-600 text-sm">
      <Loader2 className="size-4 animate-spin" />
      <span>{label}</span>
    </div>
  );
}

export function ErrorBox({ error, onRetry }: { error: unknown; onRetry?: () => void }) {
  const msg = error instanceof Error ? error.message : "Error desconocido";
  return (
    <div className="rise flex items-start gap-3 rounded-lg border border-bad-600/30 bg-bad-100 px-4 py-3 text-sm text-bad-600" data-testid="error-box">
      <AlertTriangle className="size-4 mt-0.5 shrink-0" />
      <div className="flex-1">
        <p className="font-semibold">Algo salió mal</p>
        <p>{msg}</p>
      </div>
      {onRetry && (
        <button onClick={onRetry} className="rounded border border-bad-600/40 px-2 py-1 text-xs font-semibold hover:bg-bad-600 hover:text-white transition-colors" data-testid="button-retry">
          Reintentar
        </button>
      )}
    </div>
  );
}

export function EmptyState({ icon, title, hint, action }: { icon: ReactNode; title: string; hint: string; action?: ReactNode }) {
  return (
    <div className="rise flex flex-col items-center justify-center rounded-xl border border-dashed border-line bg-card py-14 px-6 text-center" data-testid="empty-state">
      <div className="mb-3 grid size-12 place-items-center rounded-full bg-caribe-50 text-caribe-600">{icon}</div>
      <h3 className="font-display text-lg font-semibold text-ink-950">{title}</h3>
      <p className="mt-1 max-w-sm text-sm text-ink-600">{hint}</p>
      {action && <div className="mt-4">{action}</div>}
    </div>
  );
}

export function Modal({ title, onClose, children, wide }: { title: string; onClose: () => void; children: ReactNode; wide?: boolean }) {
  useEffect(() => {
    const h = (e: KeyboardEvent) => e.key === "Escape" && onClose();
    window.addEventListener("keydown", h);
    return () => window.removeEventListener("keydown", h);
  }, [onClose]);
  return (
    <div className="fadein fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-ink-950/50 p-4 backdrop-blur-sm" onMouseDown={(e) => e.target === e.currentTarget && onClose()}>
      <div className={`rise my-8 w-full ${wide ? "max-w-3xl" : "max-w-lg"} rounded-xl border border-line bg-card shadow-2xl`}>
        <div className="flex items-center justify-between border-b border-line px-5 py-3">
          <h2 className="font-display text-base font-bold text-ink-950">{title}</h2>
          <button onClick={onClose} className="rounded p-1 text-ink-600 hover:bg-paper hover:text-ink-950 transition-colors" data-testid="button-close-modal">
            <X className="size-4" />
          </button>
        </div>
        <div className="p-5">{children}</div>
      </div>
    </div>
  );
}

export function Field({ label, children, hint }: { label: string; children: ReactNode; hint?: string }) {
  return (
    <label className="block text-sm">
      <span className="mb-1 block font-semibold text-ink-800">{label}</span>
      {children}
      {hint && <span className="mt-1 block text-xs text-ink-600">{hint}</span>}
    </label>
  );
}

export const inputCls =
  "w-full rounded-md border border-line bg-white px-3 py-1.5 text-sm text-ink-950 outline-none transition-shadow focus:border-caribe-500 focus:ring-2 focus:ring-caribe-500/25 placeholder:text-ink-600/50";

export const btnPrimary =
  "inline-flex items-center gap-1.5 rounded-md bg-mango-500 px-3.5 py-1.5 text-sm font-bold text-ink-950 shadow-sm transition-all hover:bg-mango-400 active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none";
export const btnSecondary =
  "inline-flex items-center gap-1.5 rounded-md border border-line bg-white px-3.5 py-1.5 text-sm font-semibold text-ink-800 transition-colors hover:border-caribe-500 hover:text-caribe-600 active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none";
export const btnDanger =
  "inline-flex items-center gap-1.5 rounded-md border border-bad-600/30 bg-white px-3.5 py-1.5 text-sm font-semibold text-bad-600 transition-colors hover:bg-bad-600 hover:text-white active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none";

export function Badge({ tone, children }: { tone: "ok" | "warn" | "bad" | "info" | "muted"; children: ReactNode }) {
  const map = {
    ok: "bg-ok-100 text-ok-600",
    warn: "bg-warn-100 text-warn-600",
    bad: "bg-bad-100 text-bad-600",
    info: "bg-caribe-100 text-caribe-600",
    muted: "bg-line/60 text-ink-600",
  } as const;
  return <span className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold ${map[tone]}`}>{children}</span>;
}

export function bookingBadgeTone(status: number): "ok" | "warn" | "bad" | "info" | "muted" {
  // 0 reservada, 1 firmada, 3 entregada, 4 devuelta, 2 cancelada
  switch (status) {
    case 0: return "warn";
    case 1: return "info";
    case 3: return "ok";
    case 4: return "muted";
    case 2: return "bad";
    default: return "muted";
  }
}

export function PageHeader({ title, sub, action }: { title: string; sub?: string; action?: ReactNode }) {
  return (
    <div className="mb-5 flex flex-wrap items-end justify-between gap-3">
      <div>
        <h1 className="font-display text-2xl font-bold tracking-tight text-ink-950">{title}</h1>
        {sub && <p className="mt-0.5 text-sm text-ink-600">{sub}</p>}
      </div>
      {action}
    </div>
  );
}

export function Confirm({ title, message, confirmLabel, onConfirm, onClose, pending, error }: {
  title: string; message: string; confirmLabel: string;
  onConfirm: () => void; onClose: () => void; pending?: boolean; error?: unknown;
}) {
  return (
    <Modal title={title} onClose={onClose}>
      <p className="text-sm text-ink-800">{message}</p>
      {error != null && <div className="mt-3"><ErrorBox error={error} /></div>}
      <div className="mt-5 flex justify-end gap-2">
        <button className={btnSecondary} onClick={onClose} data-testid="button-cancel-confirm">Cancelar</button>
        <button className={btnDanger} onClick={onConfirm} disabled={pending} data-testid="button-confirm">
          {pending ? "Procesando…" : confirmLabel}
        </button>
      </div>
    </Modal>
  );
}
