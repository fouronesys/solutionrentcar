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
    id: "caobanico",
    city: "San José de las Matas",
    name: "Casa Rivas RentCar",
    address: "Cerca del km 16, Caobanico, San José de las Matas, Santiago",
    hours: "Coordina tu entrega por WhatsApp",
    phone: "+1 829-474-4659",
    lat: 19.3610,
    lng: -71.0103,
  },
];

export const CITIES = ["San José de las Matas"];

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
