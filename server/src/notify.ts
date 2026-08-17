import { q } from "./db.js";

export const EVENTS = {
  BOOKING_CREATED: "booking_created",
  BOOKING_CANCELED: "booking_canceled",
  BOOKING_SIGNED: "booking_signed",
  BOOKING_DELIVERED: "booking_delivered",
  BOOKING_RETURNED: "booking_returned",
} as const;

async function sendExpoPush(tokens: string[], title: string, body: string, data: unknown): Promise<void> {
  const valid = tokens.filter((t) => t.startsWith("ExponentPushToken"));
  if (!valid.length) return;
  try {
    await fetch("https://exp.host/--/api/v2/push/send", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(valid.map((to) => ({ to, title, body, data, sound: "default" }))),
    });
  } catch (e) {
    console.error("[push] fallo al enviar:", e);
  }
}

async function isEnabled(companyId: number, recipientType: string, recipientId: number, eventType: string, channel: string): Promise<boolean> {
  const r = await q(
    `SELECT enabled FROM notification_preferences
     WHERE company_id=$1 AND recipient_type=$2 AND recipient_id=$3 AND event_type=$4 AND channel=$5`,
    [companyId, recipientType, recipientId, eventType, channel],
  );
  if (!r.rows.length) return true; // default: enabled
  return !!r.rows[0].enabled;
}

/** Notifica a un destinatario: inserta registro + push (si tiene tokens y preferencia activa). */
export async function notify(
  companyId: number,
  recipientType: "user" | "client",
  recipientId: number,
  eventType: string,
  title: string,
  body: string,
  data: Record<string, unknown> = {},
): Promise<void> {
  try {
    if (!(await isEnabled(companyId, recipientType, recipientId, eventType, "inapp"))) return;
    await q(
      `INSERT INTO notifications (company_id, recipient_type, recipient_id, type, title, body, url, data)
       VALUES ($1,$2,$3,$4,$5,$6,$7,$8)`,
      [companyId, recipientType, recipientId, eventType, title, body, String(data.url ?? ""), JSON.stringify(data)],
    );
    if (await isEnabled(companyId, recipientType, recipientId, eventType, "push")) {
      const r = await q(
        "SELECT token FROM device_tokens WHERE company_id=$1 AND recipient_type=$2 AND recipient_id=$3",
        [companyId, recipientType, recipientId],
      );
      await sendExpoPush(r.rows.map((x) => x.token), title, body, data);
    }
  } catch (e) {
    console.error("[notify] fallo:", e);
  }
}

/** Notifica a todo el staff activo de la empresa. */
export async function notifyCompanyStaff(
  companyId: number,
  eventType: string,
  title: string,
  body: string,
  data: Record<string, unknown> = {},
): Promise<void> {
  const r = await q("SELECT id FROM users WHERE company_id=$1 AND status=1", [companyId]);
  await Promise.all(
    r.rows.map((u) => notify(companyId, "user", Number(u.id), eventType, title, body, data)),
  );
}
