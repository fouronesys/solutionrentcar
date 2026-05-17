import React, { useState } from "react";
import { Alert, KeyboardAvoidingView, Platform, ScrollView, StyleSheet, Text, View } from "react-native";
import { Button } from "@/components/Button";
import { Input } from "@/components/Input";
import { Card } from "@/components/Card";
import { api, ApiError } from "@/api/client";
import { useAuth } from "@/auth/AuthContext";
import type { Profile } from "@/api/types";
import { colors } from "@/theme/colors";
import { i18n, setLocale, t } from "@/i18n";

export default function ProfileScreen() {
  const { user, role, logout, setUser } = useAuth();
  const [form, setForm] = useState<Partial<Profile>>(user ?? {});
  const [saving, setSaving] = useState(false);
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
  section: { fontSize: 16, fontWeight: "700", marginBottom: 10, color: colors.text },
});
