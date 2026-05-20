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
import { Button } from "@/components/Button";
import { Input } from "@/components/Input";
import { Card } from "@/components/Card";
import { api, ApiError } from "@/api/client";
import { useAuth } from "@/auth/AuthContext";
import type { ClientDocuments, Profile } from "@/api/types";
import { colors } from "@/theme/colors";
import { i18n, setLocale, t } from "@/i18n";

type DocKind = keyof ClientDocuments; // 'cedula' | 'passport' | 'license' | 'home'

const DOC_KINDS: DocKind[] = ["cedula", "license", "passport", "home"];

export default function ProfileScreen() {
  const { user, role, logout, setUser } = useAuth();
  const [form, setForm] = useState<Partial<Profile>>(user ?? {});
  const [saving, setSaving] = useState(false);
  const [uploading, setUploading] = useState<DocKind | null>(null);
  const [lang, setLang] = useState<"es" | "en">(i18n.locale === "en" ? "en" : "es");

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
        ? await ImagePicker.launchCameraAsync({ base64: true, quality: 0.7, mediaTypes: ImagePicker.MediaTypeOptions.Images })
        : await ImagePicker.launchImageLibraryAsync({ base64: true, quality: 0.7, mediaTypes: ImagePicker.MediaTypeOptions.Images });
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

  const docs = user?.documents ?? {};

  return (
    <View style={{ flex: 1, backgroundColor: colors.bg }}>
      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={{ padding: 16 }} keyboardShouldPersistTaps="handled">
          <Card>
            <Text style={styles.section}>{t("profile.title")}</Text>
            <Input label={t("profile.name")} value={form.name ?? ""} onChangeText={(v) => setForm({ ...form, name: v })} />
            <Input
              label={t("profile.lastname")}
              value={form.lastname ?? ""}
              onChangeText={(v) => setForm({ ...form, lastname: v })}
            />
            <Input
              label={t("profile.email")}
              value={form.email ?? ""}
              onChangeText={(v) => setForm({ ...form, email: v })}
              autoCapitalize="none"
              keyboardType="email-address"
            />
            <Input
              label={t("profile.phone")}
              value={form.phone ?? ""}
              onChangeText={(v) => setForm({ ...form, phone: v })}
              keyboardType="phone-pad"
            />
            <Button title={t("profile.save")} onPress={save} loading={saving} />
          </Card>

          {role === "client" ? (
            <Card>
              <Text style={styles.section}>{t("documents.title")}</Text>
              <Text style={styles.subtle}>{t("documents.subtitle")}</Text>
              {DOC_KINDS.map((k) => {
                const url = docs[k];
                const isUploading = uploading === k;
                return (
                  <View key={k} style={styles.docRow}>
                    <View style={styles.docMain}>
                      {url ? (
                        <Image source={{ uri: url }} style={styles.docThumb} />
                      ) : (
                        <View style={[styles.docThumb, styles.docThumbEmpty]}>
                          <Text style={{ color: colors.textMuted, fontSize: 22 }}>📄</Text>
                        </View>
                      )}
                      <View style={{ flex: 1, marginLeft: 10 }}>
                        <Text style={styles.docName}>{t(`documents.${k}`)}</Text>
                        <Text style={[styles.docStatus, url ? styles.docOk : styles.docPending]}>
                          {url ? t("documents.uploaded") : t("documents.missing")}
                        </Text>
                      </View>
                    </View>
                    <View style={styles.docActions}>
                      {url ? (
                        <Pressable style={styles.docBtnSec} onPress={() => Linking.openURL(url)}>
                          <Text style={styles.docBtnSecText}>{t("documents.view")}</Text>
                        </Pressable>
                      ) : null}
                      <Pressable style={styles.docBtn} onPress={() => askDocSource(k)} disabled={isUploading}>
                        {isUploading ? (
                          <ActivityIndicator color="#fff" />
                        ) : (
                          <Text style={styles.docBtnText}>{url ? t("documents.replace") : t("documents.upload")}</Text>
                        )}
                      </Pressable>
                    </View>
                  </View>
                );
              })}
            </Card>
          ) : null}

          <Card>
            <Text style={styles.section}>{t("profile.language")}</Text>
            <View style={{ flexDirection: "row" }}>
              <Button
                title="Español"
                variant={lang === "es" ? "primary" : "secondary"}
                onPress={() => { setLocale("es"); setLang("es"); }}
                style={{ flex: 1, marginRight: 6 }}
              />
              <Button
                title="English"
                variant={lang === "en" ? "primary" : "secondary"}
                onPress={() => { setLocale("en"); setLang("en"); }}
                style={{ flex: 1, marginLeft: 6 }}
              />
            </View>
          </Card>

          <Text style={{ color: colors.textMuted, marginVertical: 8, textAlign: "center" }}>
            {role === "staff" ? "Staff" : "Cliente"} · ID #{user?.id ?? "-"}
          </Text>

          <Button title={t("common.logout")} variant="danger" onPress={logout} />
        </ScrollView>
      </KeyboardAvoidingView>
    </View>
  );
}

const styles = StyleSheet.create({
  section: { fontSize: 16, fontWeight: "700", marginBottom: 6, color: colors.text },
  subtle: { color: colors.textMuted, fontSize: 13, marginBottom: 12 },
  docRow: {
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
  },
  docMain: { flexDirection: "row", alignItems: "center" },
  docThumb: { width: 56, height: 56, borderRadius: 8, backgroundColor: "#f1f5f9" },
  docThumbEmpty: { alignItems: "center", justifyContent: "center" },
  docName: { color: colors.text, fontWeight: "600", fontSize: 14 },
  docStatus: { fontSize: 12, marginTop: 2 },
  docOk: { color: colors.success ?? "#16a34a" },
  docPending: { color: colors.textMuted },
  docActions: { flexDirection: "row", justifyContent: "flex-end", marginTop: 8 },
  docBtn: {
    backgroundColor: colors.primary,
    paddingHorizontal: 14,
    paddingVertical: 8,
    borderRadius: 6,
    marginLeft: 6,
    minWidth: 90,
    alignItems: "center",
  },
  docBtnText: { color: "#fff", fontWeight: "600", fontSize: 13 },
  docBtnSec: {
    backgroundColor: "transparent",
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 6,
    borderWidth: 1,
    borderColor: colors.border,
  },
  docBtnSecText: { color: colors.text, fontWeight: "600", fontSize: 13 },
});
