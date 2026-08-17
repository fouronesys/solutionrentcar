// 404 — hasta perderse debe verse bien.
import { Link } from "react-router-dom";
import { Compass } from "lucide-react";
import { btnPrimary } from "../ui";

export default function NotFound() {
  return (
    <div className="rise flex min-h-[60vh] flex-col items-center justify-center text-center">
      <div className="mb-4 grid size-16 place-items-center rounded-2xl bg-caribe-100 text-caribe-600">
        <Compass className="size-8" />
      </div>
      <p className="font-mono text-sm font-bold text-mango-600">Error 404</p>
      <h1 className="mt-1 font-display text-3xl font-bold text-ink-950">Esta ruta no existe en el mapa</h1>
      <p className="mt-2 max-w-sm text-sm text-ink-600">La página que buscas no está en el panel. Vuelve al inicio y sigue con tu operación.</p>
      <Link to="/" className={btnPrimary + " mt-5"} data-testid="link-home">Volver al inicio</Link>
    </div>
  );
}
