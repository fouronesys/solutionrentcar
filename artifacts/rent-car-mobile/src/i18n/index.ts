import { I18n } from "i18n-js";
import * as Localization from "expo-localization";
import "intl-pluralrules";
import en from "./en.json";
import es from "./es.json";

export const i18n = new I18n({ en, es });
i18n.defaultLocale = "es";
i18n.enableFallback = true;

const sys = Localization.getLocales()?.[0]?.languageCode ?? "es";
i18n.locale = sys === "en" ? "en" : "es";

export function t(key: string, opts?: Record<string, unknown>) {
  return i18n.t(key, opts);
}

export function setLocale(locale: "es" | "en") {
  i18n.locale = locale;
}
