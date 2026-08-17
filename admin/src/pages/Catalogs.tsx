// Catálogos auxiliares: pestañas por tipo, edición en línea.
import { useState, type FormEvent } from "react";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { Check, Layers, Pencil, Plus, Trash2, X } from "lucide-react";
import {
  addCatalogItem, CATALOG_KINDS, CATALOG_LABELS, deleteCatalogItem, listCatalog,
  updateCatalogItem, type CatalogKind,
} from "../api/endpoints";
import { btnPrimary, Confirm, EmptyState, ErrorBox, inputCls, PageHeader, Spinner } from "../ui";

export default function Catalogs() {
  const [kind, setKind] = useState<CatalogKind>("brands");
  const qc = useQueryClient();
  const q = useQuery({ queryKey: ["catalog", kind], queryFn: () => listCatalog(kind) });

  const [newName, setNewName] = useState("");
  const [editId, setEditId] = useState<number | null>(null);
  const [editName, setEditName] = useState("");
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const invalidate = () => qc.invalidateQueries({ queryKey: ["catalog", kind] });

  const add = useMutation({
    mutationFn: (name: string) => addCatalogItem(kind, name),
    onSuccess: () => { setNewName(""); invalidate(); },
  });
  const rename = useMutation({
    mutationFn: ({ id, name }: { id: number; name: string }) => updateCatalogItem(kind, id, name),
    onSuccess: () => { setEditId(null); invalidate(); },
  });
  const remove = useMutation({
    mutationFn: (id: number) => deleteCatalogItem(kind, id),
    onSuccess: () => { setDeleteId(null); invalidate(); },
  });

  const submitAdd = (e: FormEvent) => {
    e.preventDefault();
    if (newName.trim()) add.mutate(newName.trim());
  };

  return (
    <div>
      <PageHeader title="Catálogos" sub="Las listas que alimentan la flota: marcas, categorías, sucursales y más." />

      <div className="mb-4 flex flex-wrap gap-1.5">
        {CATALOG_KINDS.map((k) => (
          <button key={k} onClick={() => { setKind(k); setEditId(null); }}
            className={`rounded-full px-3.5 py-1.5 text-sm font-bold transition-colors ${
              k === kind ? "bg-ink-950 text-white" : "bg-card border border-line text-ink-800 hover:border-caribe-500 hover:text-caribe-600"
            }`}
            data-testid={`tab-catalog-${k}`}>
            {CATALOG_LABELS[k]}
          </button>
        ))}
      </div>

      <div className="max-w-2xl">
        <form onSubmit={submitAdd} className="mb-3 flex gap-2">
          <input className={inputCls} value={newName} onChange={(e) => setNewName(e.target.value)}
            placeholder={`Agregar a ${CATALOG_LABELS[kind].toLowerCase()}…`} data-testid="input-new-catalog-item" />
          <button type="submit" className={btnPrimary} disabled={add.isPending || !newName.trim()} data-testid="button-add-catalog-item">
            <Plus className="size-4" /> Agregar
          </button>
        </form>
        {add.error != null && <div className="mb-3"><ErrorBox error={add.error} /></div>}
        {rename.error != null && <div className="mb-3"><ErrorBox error={rename.error} /></div>}

        {q.isLoading && <Spinner />}
        {q.error != null && <ErrorBox error={q.error} onRetry={() => q.refetch()} />}

        {q.data && q.data.length === 0 && (
          <EmptyState icon={<Layers className="size-6" />} title={`Sin ${CATALOG_LABELS[kind].toLowerCase()} todavía`}
            hint="Agrega el primer elemento arriba; luego podrás usarlo al registrar vehículos." />
        )}

        {q.data && q.data.length > 0 && (
          <ul className="overflow-hidden rounded-xl border border-line bg-card">
            {q.data.map((item) => (
              <li key={item.id} className="flex items-center gap-2 border-b border-line/60 px-4 py-2 last:border-0 transition-colors hover:bg-caribe-50/60" data-testid={`row-catalog-${item.id}`}>
                {editId === item.id ? (
                  <>
                    <input className={inputCls} value={editName} onChange={(e) => setEditName(e.target.value)} autoFocus data-testid="input-edit-catalog-item" />
                    <button className="rounded p-1.5 text-ok-600 hover:bg-ok-100 transition-colors" disabled={rename.isPending}
                      onClick={() => rename.mutate({ id: item.id, name: editName.trim() })} data-testid="button-save-rename">
                      <Check className="size-4" />
                    </button>
                    <button className="rounded p-1.5 text-ink-600 hover:bg-paper transition-colors" onClick={() => setEditId(null)}>
                      <X className="size-4" />
                    </button>
                  </>
                ) : (
                  <>
                    <span className="flex-1 text-sm font-semibold text-ink-950">{item.name}</span>
                    <button className="rounded p-1.5 text-ink-600 transition-colors hover:bg-caribe-100 hover:text-caribe-600"
                      onClick={() => { setEditId(item.id); setEditName(item.name); }} data-testid={`button-rename-${item.id}`}>
                      <Pencil className="size-3.5" />
                    </button>
                    <button className="rounded p-1.5 text-ink-600 transition-colors hover:bg-bad-100 hover:text-bad-600"
                      onClick={() => setDeleteId(item.id)} data-testid={`button-delete-${item.id}`}>
                      <Trash2 className="size-3.5" />
                    </button>
                  </>
                )}
              </li>
            ))}
          </ul>
        )}
      </div>

      {deleteId !== null && (
        <Confirm title="Eliminar elemento" message="Se eliminará del catálogo. Los vehículos que lo usen podrían quedar sin este atributo."
          confirmLabel="Eliminar" pending={remove.isPending} error={remove.error}
          onConfirm={() => remove.mutate(deleteId)} onClose={() => { setDeleteId(null); remove.reset(); }} />
      )}
    </div>
  );
}
