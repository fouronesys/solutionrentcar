// Inicio de sesión — la puerta del cockpit.
import { useState, type FormEvent } from "react";
import { useNavigate } from "react-router-dom";
import { KeyRound, Loader2 } from "lucide-react";
import { login } from "../api/endpoints";
import { errMsg } from "../lib";
import { Field, inputCls } from "../ui";

export default function Login() {
  const navigate = useNavigate();
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [company, setCompany] = useState("");
  const [pending, setPending] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const submit = async (e: FormEvent) => {
    e.preventDefault();
    setPending(true);
    setError(null);
    try {
      const res = await login(username.trim(), password, company.trim() || null);
      navigate(res.is_super ? "/companies" : "/", { replace: true });
    } catch (err) {
      setError(errMsg(err));
    } finally {
      setPending(false);
    }
  };

  return (
    <div className="flex min-h-[100dvh] items-center justify-center bg-ink-950 p-4"
      style={{ backgroundImage: "radial-gradient(ellipse 60% 50% at 70% 10%, rgba(17,147,173,0.25), transparent), radial-gradient(ellipse 40% 40% at 10% 90%, rgba(245,146,17,0.12), transparent)" }}>
      <div className="rise w-full max-w-sm">
        <div className="mb-6 text-center">
          <div className="mx-auto mb-3 grid size-12 place-items-center rounded-xl bg-mango-500 font-display text-lg font-extrabold text-ink-950 shadow-lg shadow-mango-500/30">RD</div>
          <h1 className="font-display text-2xl font-bold text-white">Panel de Plataforma</h1>
          <p className="mt-1 text-sm text-ink-100/60">Movilidad rent-car de República Dominicana</p>
        </div>
        <form onSubmit={submit} className="space-y-4 rounded-xl border border-ink-700 bg-ink-900 p-6 shadow-2xl">
          <Field label="Usuario">
            <input className={inputCls} value={username} onChange={(e) => setUsername(e.target.value)} autoFocus autoComplete="username" required data-testid="input-username" />
          </Field>
          <Field label="Contraseña">
            <input className={inputCls} type="password" value={password} onChange={(e) => setPassword(e.target.value)} autoComplete="current-password" required data-testid="input-password" />
          </Field>
          <Field label="Empresa" hint="Slug de tu empresa. Déjalo vacío si eres super admin de la plataforma.">
            <input className={inputCls} value={company} onChange={(e) => setCompany(e.target.value)} placeholder="mi-rentcar" data-testid="input-company" />
          </Field>
          {error && (
            <p className="rounded-md border border-bad-600/40 bg-bad-100 px-3 py-2 text-sm font-semibold text-bad-600" data-testid="text-login-error">{error}</p>
          )}
          <button type="submit" disabled={pending}
            className="flex w-full items-center justify-center gap-2 rounded-md bg-mango-500 py-2.5 font-display text-sm font-bold text-ink-950 transition-all hover:bg-mango-400 active:scale-[0.99] disabled:opacity-60"
            data-testid="button-login">
            {pending ? <Loader2 className="size-4 animate-spin" /> : <KeyRound className="size-4" />}
            {pending ? "Entrando…" : "Entrar al panel"}
          </button>
        </form>
        <style>{`form label span { color: #d8e4ee; } form label .text-ink-600 { color: rgba(216,228,238,0.45); }`}</style>
      </div>
    </div>
  );
}
