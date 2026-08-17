// Punto de entrada del panel: sesión, roles y estructura de rutas.
import { useEffect, useState } from "react";
import { BrowserRouter, Navigate, Route, Routes } from "react-router-dom";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { getSession, subscribeSession } from "./api/client";
import type { Session } from "./api/types";
import Shell from "./layout";
import Login from "./pages/Login";
import Dashboard from "./pages/Dashboard";
import Fleet from "./pages/Fleet";
import Bookings from "./pages/Bookings";
import Catalogs from "./pages/Catalogs";
import Staff from "./pages/Staff";
import Companies from "./pages/Companies";
import CompanyForm from "./pages/CompanyForm";
import NotFound from "./pages/NotFound";

const queryClient = new QueryClient({
  defaultOptions: { queries: { retry: 1, refetchOnWindowFocus: false } },
});

export default function App() {
  const [session, setSession] = useState<Session | null>(() => getSession());

  useEffect(() => {
    subscribeSession((s) => {
      setSession(s);
      if (!s) queryClient.clear();
    });
  }, []);

  return (
    <QueryClientProvider client={queryClient}>
      <BrowserRouter>
        {!session ? (
          <Routes>
            <Route path="/login" element={<Login />} />
            <Route path="*" element={<Navigate to="/login" replace />} />
          </Routes>
        ) : (
          <Shell session={session}>
            <Routes>
              <Route path="/login" element={<Navigate to={session.isSuper ? "/companies" : "/"} replace />} />
              {session.isSuper ? (
                <>
                  <Route path="/" element={<Navigate to="/companies" replace />} />
                  <Route path="/companies" element={<Companies />} />
                  <Route path="/companies/new" element={<CompanyForm />} />
                  <Route path="/companies/:id" element={<CompanyForm />} />
                </>
              ) : (
                <>
                  <Route path="/" element={<Dashboard />} />
                  <Route path="/fleet" element={<Fleet />} />
                  <Route path="/bookings" element={<Bookings />} />
                  <Route path="/catalogs" element={<Catalogs />} />
                  <Route path="/staff" element={<Staff />} />
                </>
              )}
              <Route path="*" element={<NotFound />} />
            </Routes>
          </Shell>
        )}
      </BrowserRouter>
    </QueryClientProvider>
  );
}
