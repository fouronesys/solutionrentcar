/**
 * Shared location constants — branches + common airports.
 *
 * Used by:
 *   • locations.tsx (branch listing + map)
 *   • book/[carId].tsx (pickup / return suggestion chips)
 *
 * Each pickup point exposes:
 *   • label — short text for the suggestion chip
 *   • value — fuller string inserted into the free-text location field
 */

export interface Branch {
  id: string;
  city: string;
  name: string;
  address: string;
  hours: string;
  phone?: string;
  lat: number;
  lng: number;
  distanceKm?: number;
}

export const BRANCHES: Branch[] = [
  {
    id: "sd-piantini",
    city: "Santo Domingo",
    name: "Santo Domingo",
    address: "Av. Abraham Lincoln 1003",
    hours: "08:00 – 20:00",
    phone: "+1 849-564-4488",
    lat: 18.4764,
    lng: -69.9312,
  },
  {
    id: "sd-gazcue",
    city: "Santo Domingo",
    name: "Santo Domingo – Gazcue",
    address: "Av. Independencia 456",
    hours: "08:00 – 20:00",
    phone: "+1 849-564-4488",
    lat: 18.4721,
    lng: -69.9019,
  },
  {
    id: "punta-cana",
    city: "Punta Cana",
    name: "Punta Cana – Aeropuerto",
    address: "Terminal Internacional AILA",
    hours: "06:00 – 22:00",
    phone: "+1 809-000-0002",
    lat: 18.5674,
    lng: -68.3597,
  },
  {
    id: "santiago",
    city: "Santiago",
    name: "Santiago – Centro",
    address: "Calle del Sol 78",
    hours: "08:00 – 20:00",
    phone: "+1 809-000-0003",
    lat: 19.4517,
    lng: -70.6970,
  },
];

export const CITIES = ["Santo Domingo", "Punta Cana", "Santiago"];

/**
 * A tappable pickup-point suggestion for the booking flow.
 *   label — short chip text
 *   value — fuller string written into the location input
 */
export interface PickupSuggestion {
  id: string;
  label: string;
  value: string;
}

// Branch-derived suggestions. The chip shows the branch name; the field
// receives a fuller "name — address" string.
const BRANCH_SUGGESTIONS: PickupSuggestion[] = BRANCHES.map((b) => ({
  id: b.id,
  label: b.name,
  value: `${b.name} — ${b.address}`,
}));

// Common airports across the island.
const AIRPORT_SUGGESTIONS: PickupSuggestion[] = [
  {
    id: "apt-sdq",
    label: "Aeropuerto SDQ",
    value: "Aeropuerto SDQ (Las Américas)",
  },
  {
    id: "apt-puj",
    label: "Aeropuerto PUJ",
    value: "Aeropuerto PUJ (Punta Cana)",
  },
  {
    id: "apt-sti",
    label: "Aeropuerto STI",
    value: "Aeropuerto STI (Cibao)",
  },
];

/** Ordered list of pickup-point suggestions (branches first, then airports). */
export const PICKUP_SUGGESTIONS: PickupSuggestion[] = [
  ...BRANCH_SUGGESTIONS,
  ...AIRPORT_SUGGESTIONS,
];
