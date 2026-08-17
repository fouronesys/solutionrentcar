import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";

// El panel corre en el puerto 5173; la API multiempresa en el 8000.
export default defineConfig({
  plugins: [react(), tailwindcss()],
  server: {
    host: "0.0.0.0",
    port: 5173,
    allowedHosts: true,
    proxy: {
      "/api": { target: "http://127.0.0.1:8000", changeOrigin: true },
      "/storage": { target: "http://127.0.0.1:8000", changeOrigin: true },
      "/files": { target: "http://127.0.0.1:8000", changeOrigin: true },
    },
  },
});
