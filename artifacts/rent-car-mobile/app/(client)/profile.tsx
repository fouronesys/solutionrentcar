import React, { useState } from "react";
import {
  ActivityIndicator,
  Alert,
  KeyboardAvoidingView,
  Linking,
  Image,
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
import { LinearGradient } from "expo-linear-gradient";
import { Button } from "@/components/Button";
import { Input } from "@/components/Input";
import { Loading } from "@/components/Loading";
import { api, ApiError } from "@/api/client";
import { useAuth } from "@/auth/AuthContext";
import type { ClientDocuments, Profile } from "@/api/types";
import { colors, font, gradients, radius, shadow, spacing, type } from "@/theme/colors";
import { i18n, setLocale, t } from "@/i18n";

type DocKind = keyof ClientDocuments;
const DOC_KINDS: DocKind[] = ["cedula", "license", "passport", "home"];

function DocIcons(): Record<DocKind, keyof typeof Ionicons.glyphMap> {
  return {
    cedula: "card-outline",
    license: "car-sport-outline",
    passport: "airplane-outline",
    home: "home-outline",
  };
}

function LoginPrompt() {
  const router = useRouter();
  return (
    <View style={styles.prompt}>
      <View style={styles.promptIcon}>
        <Ionicons name="person-outline" size={38} color={colors.primaryDark} />
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
  const [form, setForm] = useState<Partial<Profile>>(user ?? {});
  const [saving, setSaving] = useState(false);
  const [uploading, setUploading] = useState<DocKind | null>(null);
  const [lang, setLang] = useState<"es" | "en">(i18n.locale === "en" ? "en" : "es");

  if (!bootstrapped) return <Loading />;

  if (!role) {
    return (
      <SafeAreaView style={styles.screen} edges={["top"]}>
        <LinearGradient colors={gradients.hero} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={styles.heroPlain}>
          <View style={styles.heroBrandRow}>
            <View style={styles.heroLogo}>
              <Image source={require("../../assets/images/logo.png")} style={{ width: 32, height: 32 }} resizeMode="contain" />
            </View>
            <Text style={styles.heroBrandLabel}>SOLUTION RENT CAR</Text>
          </View>
          <Text style={styles.heroTitle}>{t("profile.title")}</Text>
        </LinearGradient>
        <LoginPrompt />
      </SafeAreaView>
    );
  }

  const save = async () => {
    setSaving(true);
    try {
      const r = await api.patch<{ user: Profile }>("/me", form);
      setUser(r.user);
      Alert.alert(t("profile.saved"));
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
  const initial = (user?.name ?? "?")[0].toUpperCase();

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={styles.body} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>
          {/* Hero header with avatar */}
          <LinearGradient colors={gradients.hero} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={styles.hero}>
            <View style={styles.heroBrandRow}>
              <View style={styles.heroLogo}>
                <Image source={require("../../assets/images/logo.png")} style={{ width: 32, height: 32 }} resizeMode="contain" />
              </View>
              <Text style={styles.heroBrandLabel}>SOLUTION RENT CAR</Text>
            </View>
            <View style={styles.avatarSection}>
              <View style={styles.avatar}>
                <Text style={styles.avatarText}>{initial}</Text>
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.avatarName} numberOfLines={1}>{user?.name} {user?.lastname ?? ""}</Text>
                <View style={styles.rolePill}>
                  <Ionicons
                    name={role === "staff" ? "briefcase" : "person"}
                    size={12}
                    color={colors.primaryLight}
                  />
                  <Text style={styles.rolePillText}>
                    {role === "staff" ? "Staff" : "Cliente"} · #{user?.id}
                  </Text>
                </View>
              </View>
            </View>
          </LinearGradient>

          {/* Personal Info */}
          <View style={styles.sectionCard}>
            <Text style={styles.sectionLabel}>{locale === "en" ? "Personal Info" : "Información personal"}</Text>
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

          {/* Documents */}
          {role === "client" ? (
            <View style={styles.sectionCard}>
              <Text style={styles.sectionLabel}>{t("documents.title")}</Text>
              <Text style={styles.sectionSubtitle}>{t("documents.subtitle")}</Text>
              {DOC_KINDS.map((k) => {
                const url = docs[k];
                const isUploading = uploading === k;
                return (
                  <View key={k} style={styles.docRow}>
                    <View style={styles.docLeft}>
                      <View style={[styles.docIconWrap, url ? styles.docIconWrapOk : null]}>
                        <Ionicons name={docIcons[k]} size={20} color={url ? colors.success : colors.textMuted} />
                      </View>
                      <View style={{ flex: 1 }}>
                        <Text style={styles.docName}>{t(`documents.${k}`)}</Text>
                        <View style={styles.docStatusRow}>
                          {url ? (
                            <Ionicons name="checkmark-circle" size={13} color={colors.success} />
                          ) : (
                            <Ionicons name="ellipse-outline" size={13} color={colors.textMuted} />
                          )}
                          <Text style={[styles.docStatus, url ? styles.docOk : styles.docPending]}>
                            {url ? t("documents.uploaded") : t("documents.missing")}
                          </Text>
                        </View>
                      </View>
                    </View>
                    <View style={styles.docActions}>
                      {url ? (
                        <Pressable onPress={() => Linking.openURL(url)} style={styles.docBtnSec}>
                          <Ionicons name="eye-outline" size={15} color={colors.textSecondary} />
                          <Text style={styles.docBtnSecText}>{t("documents.view")}</Text>
                        </Pressable>
                      ) : null}
                      <Pressable onPress={() => askDocSource(k)} disabled={isUploading} style={styles.docBtn}>
                        {isUploading ? (
                          <ActivityIndicator color="#1A1100" size="small" />
                        ) : (
                          <>
                            <Ionicons name={url ? "refresh" : "cloud-upload-outline"} size={15} color="#1A1100" />
                            <Text style={styles.docBtnText}>{url ? t("documents.replace") : t("documents.upload")}</Text>
                          </>
                        )}
                      </Pressable>
                    </View>
                  </View>
                );
              })}
            </View>
          ) : null}

          {/* Language */}
          <View style={styles.sectionCard}>
            <Text style={styles.sectionLabel}>{t("profile.language")}</Text>
            <View style={styles.langRow}>
              <Pressable
                onPress={() => { setLocale("es"); setLang("es"); }}
                style={[styles.langBtn, lang === "es" && styles.langBtnActive]}
              >
                <Ionicons
                  name={lang === "es" ? "checkmark-circle" : "ellipse-outline"}
                  size={18}
                  color={lang === "es" ? colors.primaryDark : colors.textMuted}
                />
                <Text style={[styles.langText, lang === "es" && styles.langTextActive]}>Español</Text>
              </Pressable>
              <Pressable
                onPress={() => { setLocale("en"); setLang("en"); }}
                style={[styles.langBtn, lang === "en" && styles.langBtnActive]}
              >
                <Ionicons
                  name={lang === "en" ? "checkmark-circle" : "ellipse-outline"}
                  size={18}
                  color={lang === "en" ? colors.primaryDark : colors.textMuted}
                />
                <Text style={[styles.langText, lang === "en" && styles.langTextActive]}>English</Text>
              </Pressable>
            </View>
          </View>

          {/* Logout */}
          <View style={styles.sectionCard}>
            <Button title={t("common.logout")} variant="danger" icon="log-out-outline" onPress={logout} />
            {role === "client" ? (
              <Pressable onPress={confirmDelete} style={styles.deleteBtn}>
                <Ionicons name="trash-outline" size={15} color={colors.danger} />
                <Text style={styles.deleteText}>{t("profile.deleteAccount")}</Text>
              </Pressable>
            ) : null}
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  body: { paddingBottom: 40 },

  hero: {
    paddingTop: 20,
    paddingBottom: 26,
    paddingHorizontal: spacing.xl,
    borderBottomLeftRadius: radius.xxl,
    borderBottomRightRadius: radius.xxl,
  },
  heroPlain: {
    paddingTop: 20,
    paddingBottom: 26,
    paddingHorizontal: spacing.xl,
    borderBottomLeftRadius: radius.xxl,
    borderBottomRightRadius: radius.xxl,
  },
  heroBrandRow: { flexDirection: "row", alignItems: "center", gap: 8, marginBottom: 18 },
  heroLogo: {
    width: 36,
    height: 36,
    borderRadius: radius.sm,
    backgroundColor: colors.card,
    alignItems: "center",
    justifyContent: "center",
    overflow: "hidden",
  },
  heroBrandLabel: { ...type.label, color: "rgba(255,255,255,0.65)" },
  heroTitle: { ...type.display, color: "#FFFFFF" },

  avatarSection: { flexDirection: "row", alignItems: "center", gap: 16 },
  avatar: {
    width: 64, height: 64, borderRadius: 32,
    backgroundColor: colors.primary,
    alignItems: "center", justifyContent: "center",
    ...shadow.gold,
  },
  avatarText: { ...type.h1, color: colors.dark, fontFamily: font.extrabold },
  avatarName: { ...type.h2, color: "#FFFFFF" },
  rolePill: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
    alignSelf: "flex-start",
    marginTop: 7,
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: radius.full,
    backgroundColor: "rgba(255,255,255,0.08)",
  },
  rolePillText: { ...type.captionMed, color: "rgba(255,255,255,0.8)" },

  sectionCard: {
    backgroundColor: colors.card,
    marginHorizontal: spacing.lg,
    marginTop: spacing.md,
    padding: spacing.lg,
    borderRadius: radius.lg,
    ...shadow.sm,
  },
  sectionLabel: { ...type.label, color: colors.textMuted, marginBottom: 14 },
  sectionSubtitle: { ...type.caption, color: colors.textMuted, marginBottom: 14, marginTop: -8 },
  nameRow: { flexDirection: "row" },

  docRow: { paddingVertical: 14, borderBottomWidth: 1, borderBottomColor: colors.borderLight },
  docLeft: { flexDirection: "row", alignItems: "center", marginBottom: 10, gap: 12 },
  docIconWrap: {
    width: 42, height: 42, borderRadius: radius.md,
    backgroundColor: colors.borderLight,
    alignItems: "center", justifyContent: "center",
  },
  docIconWrapOk: { backgroundColor: colors.successBg },
  docName: { ...type.title, color: colors.text },
  docStatusRow: { flexDirection: "row", alignItems: "center", gap: 4, marginTop: 3 },
  docStatus: { ...type.small },
  docOk: { color: colors.success },
  docPending: { color: colors.textMuted },
  docActions: { flexDirection: "row", gap: 8 },
  docBtn: {
    flex: 1, flexDirection: "row", alignItems: "center", justifyContent: "center", gap: 6,
    backgroundColor: colors.primary, paddingVertical: 10,
    borderRadius: radius.md, minHeight: 40,
  },
  docBtnText: { ...type.captionMed, color: "#1A1100", fontFamily: font.bold },
  docBtnSec: {
    flex: 1, flexDirection: "row", alignItems: "center", justifyContent: "center", gap: 6,
    backgroundColor: colors.bg, paddingVertical: 10,
    borderRadius: radius.md, borderWidth: 1, borderColor: colors.border,
  },
  docBtnSecText: { ...type.captionMed, color: colors.textSecondary, fontFamily: font.semibold },

  langRow: { flexDirection: "row", gap: 10 },
  langBtn: {
    flex: 1, flexDirection: "row", alignItems: "center", justifyContent: "center",
    paddingVertical: 14, borderRadius: radius.md,
    borderWidth: 1.5, borderColor: colors.border,
    backgroundColor: colors.bg, gap: 8,
  },
  langBtnActive: { borderColor: colors.primary, backgroundColor: colors.primaryXLight },
  langText: { ...type.bodyMed, color: colors.textSecondary },
  langTextActive: { color: colors.primaryDark, fontFamily: font.semibold },

  deleteBtn: { marginTop: 16, paddingVertical: 12, flexDirection: "row", alignItems: "center", justifyContent: "center", gap: 6 },
  deleteText: { ...type.bodyMed, color: colors.danger, fontFamily: font.semibold },

  prompt: { flex: 1, alignItems: "center", justifyContent: "center", padding: spacing.xxl },
  promptIcon: {
    width: 84, height: 84, borderRadius: radius.full,
    backgroundColor: colors.primaryXLight,
    alignItems: "center", justifyContent: "center", marginBottom: 20,
    borderWidth: 1, borderColor: colors.primaryLight,
  },
  promptTitle: { ...type.h2, color: colors.text, textAlign: "center", marginBottom: 8 },
  promptSub: { ...type.callout, color: colors.textMuted, textAlign: "center", marginBottom: 28 },
  registerLink: { ...type.callout, color: colors.textMuted },
  registerLinkBold: { color: colors.primaryDark, fontFamily: font.bold },
});
