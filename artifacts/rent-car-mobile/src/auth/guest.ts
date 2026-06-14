// Read-only "guest" client account used to browse the public catalog without
// the user logging in. These EXPO_PUBLIC_* values are intentionally embedded in
// the client bundle (they are not secret — the account is read-only and only
// exists so anyone can view the car catalog before signing in to reserve).
export const GUEST_USERNAME = process.env.EXPO_PUBLIC_GUEST_USERNAME ?? "";
export const GUEST_PASSWORD = process.env.EXPO_PUBLIC_GUEST_PASSWORD ?? "";
export const guestEnabled = GUEST_USERNAME.length > 0 && GUEST_PASSWORD.length > 0;
