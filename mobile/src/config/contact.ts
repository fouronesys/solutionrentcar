/**
 * Contact configuration — WhatsApp for booking confirmations.
 */
export const WHATSAPP_NUMBER = "18495644488";

/**
 * Builds a wa.me deep link with a URL-encoded prefilled message.
 */
export function whatsappUrl(message: string): string {
  return `https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(message)}`;
}
