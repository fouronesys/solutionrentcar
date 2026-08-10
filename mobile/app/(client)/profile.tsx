/**
 * Perfil — client profile screen.
 * Design: white header, dark avatar square, upcoming booking dark card,
 * loyalty level card (blue tint), menu items, red sign-out link.
 */
import React, { useState } from "react";
import {
  ActivityIndicator,
  Alert,
  KeyboardAvoidingView,
  Linking,
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from "react-native";
import * as ImagePicker from "expo-image-picker";
import { useRouter } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { Ionicons } from "@expo/vector-icons";
import { Button } from "@/components/Button";
import { Input } from "@/components/Input";
import { Loading } from "@/components/Loading";
import { ScreenHeader } from "@/components/ScreenHeader";
import { api, ApiError } from "@/api/client";
import { useAuth } from "@/auth/AuthContext";
import type { ClientDocuments, Profile } from "@/api/types";
import { colors, font, radius, shadow, spacing, type } from "@/theme/colors";
import { useTheme, useThemedStyles } from "@/theme/ThemeContext";
import { i18n, setLocale, t } from "@/i18n";

type DocKind = keyof ClientDocuments;
const DOC_KINDS: DocKind[] = ["cedula", "license", "passport", "home"];
function DocIcons(): Record<DocKind, keyof typeof Ionicons.glyphMap> {
  return { cedula: "card-outline", license: "car-sport-outline", passport: "airplane-outline", home: "home-outline" };
}

function AppearanceSelector() {
  const styles = useThemedStyles(makeStyles);
  const { mode, setMode } = useTheme();
  const locale = i18n.locale === "en" ? "en" : "es";
  return (
    <>
      <Text style={styles.sectionTitle}>
        {locale === "en" ? "APPEARANCE" : "APARIENCIA"}
      </Text>
      <View style={styles.langRow}>
        {(["dark", "light"] as const).map((m) => (
          <Pressable
            key={m}
            onPress={() => setMode(m)}
            style={[styles.langBtn, mode === m && styles.langBtnActive]}
          >
            <Ionicons
              name={m === "dark" ? "moon" : "sunny"}
              size={18}
              color={mode === m ? colors.cta : colors.textMuted}
            />
            <Text style={[styles.langText, mode === m && styles.langTextActive]}>
              {m === "dark"
                ? locale === "en" ? "Dark" : "Oscuro"
                : locale === "en" ? "Light" : "Claro"}
            </Text>
          </Pressable>
        ))}
      </View>
    </>
  );
}

function LoginPrompt() {
  const router = useRouter();
  const styles = useThemedStyles(makeStyles);
  const locale = i18n.locale === "en" ? "en" : "es";
  return (
    <View style={styles.prompt}>
      <View style={styles.promptIcon}>
        <Ionicons name="person-outline" size={38} color={colors.cta} />
      </View>
      <Text style={styles.promptTitle}>{t("login.requiredTitle")}</Text>
      <Text style={styles.promptSub}>{t("login.requiredSubtitle")}</Text>
      <Button title={t("login.goToLogin")} onPress={() => router.push("/login/client")} size="lg" style={{ alignSelf: "stretch", marginBottom: 12 }} />
      <Pressable onPress={() => router.push("/register/client")}>
        <Text style={styles.registerLink}>
          {t("login.noAccount")} <Text style={styles.registerLinkBold}>{t("login.createAccount")}</Text>
        </Text>
      </Pressable>
    </View>
  );
}

export default function ProfileScreen() {
  const { user, role, bootstrapped, logout, setUser } = useAuth();
  const router = useRouter();
  const styles = useThemedStyles(makeStyles);
  const { mode, setMode } = useTheme();
  const [form, setForm] = useState<Partial<Profile>>(user ?? {});
  const [saving, setSaving] = useState(false);
  const [uploading, setUploading] = useState<DocKind | null>(null);
  const [lang, setLang] = useState<"es" | "en">(i18n.locale === "en" ? "en" : "es");
  const [editOpen, setEditOpen] = useState(false);

  if (!bootstrapped) return <Loading />;

  if (!role) {
    return (
      <SafeAreaView style={styles.screen} edges={["top"]}>
        <ScreenHeader
          title={t("profile.title")}
          subtitle={i18n.locale === "en" ? "Your Yowell account" : "Tu cuenta Yowell"}
        />
        <ScrollView contentContainerStyle={{ flexGrow: 1 }} showsVerticalScrollIndicator={false}>
          <LoginPrompt />
          <View style={{ paddingBottom: 24 }}>
            <AppearanceSelector />
          </View>
        </ScrollView>
      </SafeAreaView>
    );
  }

  const save = async () => {
    setSaving(true);
    try {
      const r = await api.patch<{ user: Profile }>("/me", form);
      setUser(r.user);
      Alert.alert(t("profile.saved"));
      setEditOpen(false);
    } catch (e) {
      Alert.alert(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setSaving(false);
    }
  };

  const pick = async (kind: DocKind, useCamera: boolean) => {
    try {
      const perm = useCamera
        ? await ImagePicker.requestCameraPermissionsAsync()
        : await ImagePicker.requestMediaLibraryPermissionsAsync();
      if (!perm.granted) return;
      const res = useCamera
        ? await ImagePicker.launchCameraAsync({ base64: true, quality: 0.7 })
        : await ImagePicker.launchImageLibraryAsync({ base64: true, quality: 0.7 });
      if (res.canceled || !res.assets?.length) return;
      const asset = res.assets[0];
      if (!asset.base64) return;
      setUploading(kind);
      const dataUrl = `data:${asset.mimeType ?? "image/jpeg"};base64,${asset.base64}`;
      const r = await api.post<{ user: Profile }>("/me/document", { kind, file: dataUrl });
      setUser(r.user);
      Alert.alert(t("documents.saved"));
    } catch (e) {
      Alert.alert(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setUploading(null);
    }
  };

  const askDocSource = (kind: DocKind) => {
    Alert.alert(t("documents.upload"), undefined, [
      { text: t("documents.fromCamera"), onPress: () => pick(kind, true) },
      { text: t("documents.fromGallery"), onPress: () => pick(kind, false) },
      { text: t("common.cancel"), style: "cancel" },
    ]);
  };

  const confirmDelete = () => {
    Alert.alert(t("profile.deleteAccountConfirmTitle"), t("profile.deleteAccountConfirmMsg"), [
      { text: t("common.cancel"), style: "cancel" },
      { text: t("profile.deleteAccount"), style: "destructive", onPress: async () => {
        try { await api.del("/me"); await logout(); }
        catch (e) { Alert.alert(e instanceof ApiError ? e.message : t("common.error")); }
      }},
    ]);
  };

  const docs = user?.documents ?? {};
  const docIcons = DocIcons();
  const locale = i18n.locale === "en" ? "en" : "es";
  const initial = ((user?.name ?? "?")[0] + (user?.lastname ? user.lastname[0] : "")).toUpperCase();
  const memberSince = user?.created_at
    ? new Date(user.created_at).toLocaleDateString(locale === "en" ? "en-US" : "es-ES", { month: "long", year: "numeric" })
    : null;

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <ScreenHeader
        title={t("profile.title")}
        subtitle={locale === "en" ? "Your Yowell account" : "Tu cuenta Yowell"}
        right={
          <Pressable hitSlop={8} onPress={() => setEditOpen((v) => !v)} style={styles.editBtn}>
            <Ionicons name="person-outline" size={20} color={colors.text} />
          </Pressable>
        }
      />
      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={styles.body} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>

          {/* Avatar + Name row */}
          <View style={styles.avatarRow}>
            <View style={styles.avatar}>
              <Text style={styles.avatarText}>{initial}</Text>
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.greet}>
                {locale === "en" ? "¡WELCOME BACK!" : "¡HOLA DE NUEVO!"}
              </Text>
              <Text style={styles.userName} numberOfLines={1}>
                {user?.name} {user?.lastname ?? ""}
              </Text>
              {memberSince ? (
                <Text style={styles.memberSince}>
                  {locale === "en" ? "Member since" : "Miembro desde"} {memberSince}
                </Text>
              ) : null}
            </View>
          </View>

          {/* Loyalty card */}
          <View style={styles.loyaltyCard}>
            <View style={styles.loyaltyLeft}>
              <Text style={styles.loyaltyEyebrow}>
                {locale === "en" ? "CURRENT LEVEL" : "NIVEL ACTUAL"}
              </Text>
              <View style={styles.loyaltyNameRow}>
                <Ionicons name="star" size={18} color="#F59E0B" />
                <Text style={styles.loyaltyName}>Ruta Local</Text>
              </View>
              <View style={styles.loyaltyBar}>
                <View style={[styles.loyaltyProgress, { width: "40%" }]} />
              </View>
              <Text style={styles.loyaltySub}>
                {locale === "en"
                  ? "1 more booking to unlock 10% off."
                  : "Una reserva más para desbloquear 10% de descuento."}
              </Text>
            </View>
            <View style={styles.loyaltyRight}>
              <Text style={styles.loyaltyCount}>2</Text>
              <Text style={styles.loyaltyCountLabel}>
                {locale === "en" ? "bookings\nthis year" : "reservas\neste año"}
              </Text>
            </View>
          </View>

          {/* Quick actions — bookings */}
          <Pressable style={styles.menuRow} onPress={() => router.push("/(client)/bookings")}>
            <View style={styles.menuIcon}>
              <Ionicons name="calendar-outline" size={20} color={colors.text} />
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.menuLabel}>
                {locale === "en" ? "My bookings" : "Mis reservas"}
              </Text>
              <Text style={styles.menuSub}>
                {locale === "en" ? "Track all reservations" : "Ver todas las reservas"}
              </Text>
            </View>
            <Ionicons name="chevron-forward" size={18} color={colors.textFaint} />
          </Pressable>

          {/* Section: Cuenta y Ayuda */}
          <Text style={styles.sectionTitle}>
            {locale === "en" ? "ACCOUNT & HELP" : "CUENTA Y AYUDA"}
          </Text>

          {[
            {
              icon: "settings-outline" as const,
              label: locale === "en" ? "Settings" : "Configuración",
              sub: locale === "en" ? "Preferences and account info" : "Preferencias y datos de cuenta",
              onPress: () => setEditOpen((v) => !v),
            },
            {
              icon: "card-outline" as const,
              label: locale === "en" ? "Payment methods" : "Métodos de pago",
              sub: locale === "en" ? "Manage your cards" : "Gestiona tus tarjetas",
              onPress: () => {},
            },
            {
              icon: "help-circle-outline" as const,
              label: locale === "en" ? "Help & support" : "Ayuda y soporte",
              sub: locale === "en" ? "We're here 24/7" : "Estamos para ayudarte 24/7",
              onPress: () => Linking.openURL("mailto:hola@yowell.do"),
            },
          ].map((item, idx) => (
            <Pressable key={idx} style={styles.menuRow} onPress={item.onPress}>
              <View style={styles.menuIcon}>
                <Ionicons name={item.icon} size={20} color={colors.text} />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.menuLabel}>{item.label}</Text>
                <Text style={styles.menuSub}>{item.sub}</Text>
              </View>
              <Ionicons name="chevron-forward" size={18} color={colors.textFaint} />
            </Pressable>
          ))}

          {/* Edit form (collapsible) */}
          {editOpen ? (
            <View style={styles.editCard}>
              <Text style={styles.editCardTitle}>
                {locale === "en" ? "Personal info" : "Información personal"}
              </Text>
              <View style={styles.nameRow}>
                <View style={{ flex: 1, marginRight: 8 }}>
                  <Input label={t("profile.name")} icon="person-outline" value={form.name ?? ""} onChangeText={(v) => setForm({ ...form, name: v })} />
                </View>
                <View style={{ flex: 1 }}>
                  <Input label={t("profile.lastname")} icon="people-outline" value={form.lastname ?? ""} onChangeText={(v) => setForm({ ...form, lastname: v })} />
                </View>
              </View>
              <Input label={t("profile.email")} icon="mail-outline" value={form.email ?? ""} onChangeText={(v) => setForm({ ...form, email: v })} autoCapitalize="none" keyboardType="email-address" />
              <Input label={t("profile.phone")} icon="call-outline" value={form.phone ?? ""} onChangeText={(v) => setForm({ ...form, phone: v })} keyboardType="phone-pad" />
              <Button title={t("profile.save")} icon="checkmark" onPress={save} loading={saving} />
            </View>
          ) : null}

          {/* Documents */}
          {role === "client" ? (
            <View style={styles.docCard}>
              <Text style={styles.editCardTitle}>{t("documents.title")}</Text>
              <Text style={styles.docSubtitle}>{t("documents.subtitle")}</Text>
              {DOC_KINDS.map((k) => {
                const url = docs[k];
                const isUploading = uploading === k;
                return (
                  <View key={k} style={styles.docRow}>
                    <View style={[styles.docIconWrap, url ? styles.docIconWrapOk : null]}>
                      <Ionicons name={docIcons[k]} size={18} color={url ? colors.success : colors.textMuted} />
                    </View>
                    <View style={{ flex: 1 }}>
                      <Text style={styles.docName}>{t(`documents.${k}`)}</Text>
                      <Text style={[styles.docStatus, url ? styles.docOk : styles.docPending]}>
                        {url ? t("documents.uploaded") : t("documents.missing")}
                      </Text>
                    </View>
                    <View style={styles.docActions}>
                      {url ? (
                        <Pressable onPress={() => Linking.openURL(url)} style={styles.docBtnSec}>
                          <Ionicons name="eye-outline" size={15} color={colors.textSecondary} />
                        </Pressable>
                      ) : null}
                      <Pressable onPress={() => askDocSource(k)} disabled={isUploading} style={styles.docBtn}>
                        {isUploading ? (
                          <ActivityIndicator color="#FFFFFF" size="small" />
                        ) : (
                          <Ionicons name={url ? "refresh" : "cloud-upload-outline"} size={15} color="#FFFFFF" />
                        )}
                      </Pressable>
                    </View>
                  </View>
                );
              })}
            </View>
          ) : null}

          {/* Language */}
          <View style={styles.langRow}>
            {(["es", "en"] as const).map((l) => (
              <Pressable
                key={l}
                onPress={() => { setLocale(l); setLang(l); }}
                style={[styles.langBtn, lang === l && styles.langBtnActive]}
              >
                <Ionicons
                  name={lang === l ? "checkmark-circle" : "ellipse-outline"}
                  size={18}
                  color={lang === l ? colors.cta : colors.textMuted}
                />
                <Text style={[styles.langText, lang === l && styles.langTextActive]}>
                  {l === "es" ? "Español" : "English"}
                </Text>
              </Pressable>
            ))}
          </View>

          {/* Appearance */}
          <AppearanceSelector />

          {/* Sign out */}
          <Pressable style={styles.logoutRow} onPress={logout}>
            <Ionicons name="log-out-outline" size={18} color={colors.cta} />
            <Text style={styles.logoutText}>{t("common.logout")}</Text>
          </Pressable>

          {role === "client" ? (
            <Pressable onPress={confirmDelete} style={styles.deleteRow}>
              <Ionicons name="trash-outline" size={14} color={colors.textMuted} />
              <Text style={styles.deleteText}>{t("profile.deleteAccount")}</Text>
            </Pressable>
          ) : null}

          <Text style={styles.privacyNote}>
            {locale === "en" ? "🔒 Your data is protected by Yowell." : "🔒 Tus datos están protegidos por Yowell."}
          </Text>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const makeStyles = () => StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  body: { paddingBottom: 48 },

  editBtn: {
    width: 40, height: 40, borderRadius: radius.full,
    alignItems: "center", justifyContent: "center",
    backgroundColor: colors.bg,
  },

  avatarRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 16,
    padding: spacing.xl,
  },
  avatar: {
    width: 64, height: 64, borderRadius: 14,
    backgroundColor: colors.dark,
    alignItems: "center", justifyContent: "center",
    ...shadow.sm,
  },
  avatarText: { fontFamily: font.extrabold, fontSize: 22, color: "#FFFFFF" },
  greet: { ...type.label, color: colors.cta, fontSize: 10, letterSpacing: 0.8, marginBottom: 4 },
  userName: { ...type.h2, color: colors.text },
  memberSince: { ...type.caption, color: colors.textMuted, marginTop: 3 },

  // Loyalty card
  loyaltyCard: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: colors.tint,
    marginHorizontal: spacing.xl,
    borderRadius: radius.xl,
    padding: spacing.lg,
    marginBottom: spacing.lg,
    borderWidth: 1,
    borderColor: colors.tintBorder,
    ...shadow.xs,
  },
  loyaltyLeft: { flex: 1, paddingRight: 12 },
  loyaltyEyebrow: { ...type.label, color: colors.primary, fontSize: 9, letterSpacing: 0.8, marginBottom: 6 },
  loyaltyNameRow: { flexDirection: "row", alignItems: "center", gap: 6, marginBottom: 10 },
  loyaltyName: { ...type.title, color: colors.text },
  loyaltyBar: { height: 4, backgroundColor: colors.tintBorder, borderRadius: 2, marginBottom: 8 },
  loyaltyProgress: { height: 4, backgroundColor: colors.primary, borderRadius: 2 },
  loyaltySub: { ...type.caption, color: colors.textSecondary, lineHeight: 17 },
  loyaltyRight: { alignItems: "flex-end" },
  loyaltyCount: { fontFamily: font.extrabold, fontSize: 28, color: colors.text },
  loyaltyCountLabel: { ...type.caption, color: colors.textMuted, textAlign: "right", lineHeight: 16 },

  // Menu rows
  menuRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 14,
    backgroundColor: colors.card,
    marginHorizontal: spacing.xl,
    marginBottom: 8,
    paddingVertical: 14,
    paddingHorizontal: 16,
    borderRadius: radius.lg,
    ...shadow.xs,
  },
  menuIcon: {
    width: 42, height: 42, borderRadius: radius.md,
    backgroundColor: colors.bg,
    alignItems: "center", justifyContent: "center",
  },
  menuLabel: { ...type.bodyMed, color: colors.text },
  menuSub: { ...type.caption, color: colors.textMuted, marginTop: 2 },

  sectionTitle: {
    ...type.label,
    color: colors.textMuted,
    paddingHorizontal: spacing.xl,
    paddingBottom: spacing.sm,
    paddingTop: spacing.md,
  },

  // Edit form card
  editCard: {
    backgroundColor: colors.card,
    marginHorizontal: spacing.xl,
    marginBottom: spacing.lg,
    padding: spacing.lg,
    borderRadius: radius.lg,
    ...shadow.sm,
  },
  editCardTitle: { ...type.title, color: colors.text, marginBottom: 14 },
  nameRow: { flexDirection: "row" },

  // Documents
  docCard: {
    backgroundColor: colors.card,
    marginHorizontal: spacing.xl,
    marginBottom: spacing.lg,
    padding: spacing.lg,
    borderRadius: radius.lg,
    ...shadow.sm,
  },
  docSubtitle: { ...type.caption, color: colors.textMuted, marginBottom: 14, marginTop: -8 },
  docRow: {
    flexDirection: "row",
    alignItems: "center",
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: colors.borderLight,
    gap: 12,
  },
  docIconWrap: {
    width: 38, height: 38, borderRadius: radius.md,
    backgroundColor: colors.borderLight,
    alignItems: "center", justifyContent: "center",
  },
  docIconWrapOk: { backgroundColor: colors.successBg },
  docName: { ...type.captionMed, color: colors.text },
  docStatus: { ...type.small, marginTop: 2 },
  docOk: { color: colors.success },
  docPending: { color: colors.textMuted },
  docActions: { flexDirection: "row", gap: 8 },
  docBtn: {
    width: 36, height: 36, borderRadius: radius.md,
    backgroundColor: colors.cta,
    alignItems: "center", justifyContent: "center",
  },
  docBtnSec: {
    width: 36, height: 36, borderRadius: radius.md,
    backgroundColor: colors.bg,
    alignItems: "center", justifyContent: "center",
    borderWidth: 1, borderColor: colors.border,
  },

  // Language
  langRow: { flexDirection: "row", gap: 10, marginHorizontal: spacing.xl, marginBottom: spacing.lg },
  langBtn: {
    flex: 1, flexDirection: "row", alignItems: "center", justifyContent: "center",
    paddingVertical: 14, borderRadius: radius.md,
    borderWidth: 1.5, borderColor: colors.border,
    backgroundColor: colors.bg, gap: 8,
  },
  langBtnActive: { borderColor: colors.cta, backgroundColor: colors.ctaXLight },
  langText: { ...type.bodyMed, color: colors.textSecondary },
  langTextActive: { color: colors.cta, fontFamily: font.semibold },

  // Sign out
  logoutRow: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 8,
    paddingVertical: 16,
    marginHorizontal: spacing.xl,
    marginBottom: 4,
  },
  logoutText: { ...type.bodyMed, color: colors.cta, fontFamily: font.semibold },
  deleteRow: {
    flexDirection: "row", alignItems: "center", justifyContent: "center", gap: 6,
    paddingVertical: 10, marginBottom: 8,
  },
  deleteText: { ...type.caption, color: colors.textMuted },
  privacyNote: { ...type.small, color: colors.textMuted, textAlign: "center", paddingBottom: 8 },

  // Login prompt
  prompt: { flex: 1, alignItems: "center", justifyContent: "center", padding: spacing.xxl },
  promptIcon: {
    width: 84, height: 84, borderRadius: radius.full,
    backgroundColor: colors.ctaXLight,
    alignItems: "center", justifyContent: "center", marginBottom: 20,
    borderWidth: 1, borderColor: colors.ctaLight,
  },
  promptTitle: { ...type.h2, color: colors.text, textAlign: "center", marginBottom: 8 },
  promptSub: { ...type.callout, color: colors.textMuted, textAlign: "center", marginBottom: 28 },
  registerLink: { ...type.callout, color: colors.textMuted },
  registerLinkBold: { color: colors.cta, fontFamily: font.bold },
});
