import React, { useState } from "react";
import {
  ActivityIndicator,
  Alert,
  Image,
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
import { Button } from "@/components/Button";
import { Input } from "@/components/Input";
import { Loading } from "@/components/Loading";
import { api, ApiError } from "@/api/client";
import { useAuth } from "@/auth/AuthContext";
import type { ClientDocuments, Profile } from "@/api/types";
import { colors, radius, shadow, spacing } from "@/theme/colors";
import { i18n, setLocale, t } from "@/i18n";

type DocKind = keyof ClientDocuments;
const DOC_KINDS: DocKind[] = ["cedula", "license", "passport", "home"];

function DocIcons(): Record<DocKind, string> {
  return { cedula: "🪪", license: "🚗", passport: "🛂", home: "🏠" };
}

function LoginPrompt() {
  const router = useRouter();
  return (
    <View style={styles.prompt}>
      <View style={styles.promptIcon}><Text style={{ fontSize: 48 }}>👤</Text></View>
      <Text style={styles.promptTitle}>{t("login.requiredTitle")}</Text>
      <Text style={styles.promptSub}>{t("login.requiredSubtitle")}</Text>
      <Button title={t("login.goToLogin")} onPress={() => router.push("/login/client")} size="lg" style={{ marginBottom: 12 }} />
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
        <View style={styles.pageHeader}>
          <Text style={styles.pageTitle}>{t("profile.title")}</Text>
        </View>
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

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <View style={styles.pageHeader}>
        <Text style={styles.pageTitle}>{t("profile.title")}</Text>
      </View>
      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={styles.body} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>
          {/* Avatar */}
          <View style={styles.avatarSection}>
            <View style={styles.avatar}>
              <Text style={styles.avatarText}>
                {(user?.name ?? "?")[0].toUpperCase()}
              </Text>
            </View>
            <View>
              <Text style={styles.avatarName}>{user?.name} {user?.lastname ?? ""}</Text>
              <Text style={styles.avatarSub}>
                {role === "staff" ? "👔 Staff" : "👤 Cliente"} · #{user?.id}
              </Text>
            </View>
          </View>

          {/* Personal Info */}
          <View style={styles.sectionCard}>
            <Text style={styles.sectionLabel}>{locale === "en" ? "Personal Info" : "Información personal"}</Text>
            <View style={styles.nameRow}>
              <View style={{ flex: 1, marginRight: 8 }}>
                <Input label={t("profile.name")} value={form.name ?? ""} onChangeText={(v) => setForm({ ...form, name: v })} />
              </View>
              <View style={{ flex: 1 }}>
                <Input label={t("profile.lastname")} value={form.lastname ?? ""} onChangeText={(v) => setForm({ ...form, lastname: v })} />
              </View>
            </View>
            <Input label={t("profile.email")} value={form.email ?? ""} onChangeText={(v) => setForm({ ...form, email: v })} autoCapitalize="none" keyboardType="email-address" />
            <Input label={t("profile.phone")} value={form.phone ?? ""} onChangeText={(v) => setForm({ ...form, phone: v })} keyboardType="phone-pad" />
            <Button title={t("profile.save")} onPress={save} loading={saving} />
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
                      <Text style={styles.docIcon}>{docIcons[k]}</Text>
                      <View style={{ flex: 1 }}>
                        <Text style={styles.docName}>{t(`documents.${k}`)}</Text>
                        <Text style={[styles.docStatus, url ? styles.docOk : styles.docPending]}>
                          {url ? `✓ ${t("documents.uploaded")}` : t("documents.missing")}
                        </Text>
                      </View>
                    </View>
                    <View style={styles.docActions}>
                      {url ? (
                        <Pressable onPress={() => Linking.openURL(url)} style={styles.docBtnSec}>
                          <Text style={styles.docBtnSecText}>{t("documents.view")}</Text>
                        </Pressable>
                      ) : null}
                      <Pressable onPress={() => askDocSource(k)} disabled={isUploading} style={styles.docBtn}>
                        {isUploading
                          ? <ActivityIndicator color={colors.dark} size="small" />
                          : <Text style={styles.docBtnText}>{url ? t("documents.replace") : t("documents.upload")}</Text>
                        }
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
                <Text style={styles.langFlag}>🇩🇴</Text>
                <Text style={[styles.langText, lang === "es" && styles.langTextActive]}>Español</Text>
              </Pressable>
              <Pressable
                onPress={() => { setLocale("en"); setLang("en"); }}
                style={[styles.langBtn, lang === "en" && styles.langBtnActive]}
              >
                <Text style={styles.langFlag}>🇺🇸</Text>
                <Text style={[styles.langText, lang === "en" && styles.langTextActive]}>English</Text>
              </Pressable>
            </View>
          </View>

          {/* Logout */}
          <View style={styles.sectionCard}>
            <Button title={t("common.logout")} variant="secondary" onPress={logout} />
            {role === "client" ? (
              <Pressable onPress={confirmDelete} style={styles.deleteBtn}>
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
  pageHeader: {
    paddingHorizontal: spacing.lg,
    paddingTop: spacing.md,
    paddingBottom: spacing.lg,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
    backgroundColor: colors.card,
  },
  pageTitle: { fontSize: 20, fontWeight: "800", color: colors.text },

  body: { paddingBottom: 40 },

  avatarSection: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: colors.dark,
    padding: spacing.xl,
    gap: 16,
  },
  avatar: {
    width: 60, height: 60, borderRadius: 30,
    backgroundColor: colors.primary,
    alignItems: "center", justifyContent: "center",
  },
  avatarText: { fontSize: 26, fontWeight: "800", color: colors.dark },
  avatarName: { fontSize: 18, fontWeight: "800", color: "#fff" },
  avatarSub: { fontSize: 13, color: "rgba(255,255,255,0.55)", marginTop: 3 },

  sectionCard: {
    backgroundColor: colors.card,
    marginTop: 8,
    padding: spacing.lg,
    borderTopWidth: 1, borderTopColor: colors.border,
    borderBottomWidth: 1, borderBottomColor: colors.border,
  },
  sectionLabel: {
    fontSize: 11, fontWeight: "700", color: colors.textMuted,
    textTransform: "uppercase", letterSpacing: 0.8, marginBottom: 14,
  },
  sectionSubtitle: { color: colors.textMuted, fontSize: 13, marginBottom: 14, marginTop: -8 },
  nameRow: { flexDirection: "row" },

  docRow: { paddingVertical: 14, borderBottomWidth: 1, borderBottomColor: colors.borderLight },
  docLeft: { flexDirection: "row", alignItems: "center", marginBottom: 8 },
  docIcon: { fontSize: 24, marginRight: 12 },
  docName: { fontSize: 15, fontWeight: "700", color: colors.text },
  docStatus: { fontSize: 12, marginTop: 2, fontWeight: "600" },
  docOk: { color: colors.success },
  docPending: { color: colors.textMuted },
  docActions: { flexDirection: "row", gap: 8 },
  docBtn: {
    flex: 1, backgroundColor: colors.primary, paddingVertical: 9,
    borderRadius: radius.md, alignItems: "center", minHeight: 38,
    justifyContent: "center",
  },
  docBtnText: { color: colors.dark, fontWeight: "700", fontSize: 13 },
  docBtnSec: {
    flex: 1, backgroundColor: colors.borderLight, paddingVertical: 9,
    borderRadius: radius.md, alignItems: "center",
    borderWidth: 1, borderColor: colors.border,
  },
  docBtnSecText: { color: colors.textSecondary, fontWeight: "600", fontSize: 13 },

  langRow: { flexDirection: "row", gap: 10 },
  langBtn: {
    flex: 1, flexDirection: "row", alignItems: "center", justifyContent: "center",
    paddingVertical: 12, borderRadius: radius.md,
    borderWidth: 1.5, borderColor: colors.border,
    backgroundColor: colors.borderLight, gap: 8,
  },
  langBtnActive: { borderColor: colors.primary, backgroundColor: colors.primaryXLight },
  langFlag: { fontSize: 20 },
  langText: { fontSize: 14, fontWeight: "600", color: colors.textSecondary },
  langTextActive: { color: colors.primaryDark },

  deleteBtn: { marginTop: 16, paddingVertical: 12, alignItems: "center" },
  deleteText: { color: colors.danger, fontWeight: "600", fontSize: 14 },

  prompt: { flex: 1, alignItems: "center", justifyContent: "center", padding: spacing.xxl },
  promptIcon: {
    width: 88, height: 88, borderRadius: 44,
    backgroundColor: colors.primaryXLight,
    alignItems: "center", justifyContent: "center", marginBottom: 20,
  },
  promptTitle: { fontSize: 20, fontWeight: "800", color: colors.text, textAlign: "center", marginBottom: 8 },
  promptSub: { color: colors.textMuted, fontSize: 14, textAlign: "center", marginBottom: 28, lineHeight: 20 },
  registerLink: { color: colors.textMuted, fontSize: 14 },
  registerLinkBold: { color: colors.primaryDark, fontWeight: "700" },
});
