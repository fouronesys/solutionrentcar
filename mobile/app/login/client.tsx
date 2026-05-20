import React, { useState } from "react";
import { Alert, KeyboardAvoidingView, Platform, Pressable, ScrollView, StyleSheet, Text, View } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { Stack, useRouter } from "expo-router";
import { Button } from "@/components/Button";
import { Input } from "@/components/Input";
import { useAuth } from "@/auth/AuthContext";
import { ApiError } from "@/api/client";
import { colors } from "@/theme/colors";
import { t } from "@/i18n";

export default function ClientLogin() {
  const router = useRouter();
  const { loginClient } = useAuth();
  const [phone, setPhone] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState(false);

  const submit = async () => {
    if (!phone.trim() || !password.trim()) {
      Alert.alert(t("login.errors.empty"));
      return;
    }
    setLoading(true);
    try {
      await loginClient(phone.trim(), password.trim());
    } catch (e) {
      const msg = e instanceof ApiError ? e.message : t("login.errors.invalid");
      Alert.alert(msg);
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.safe}>
      <Stack.Screen options={{ headerShown: true, title: t("login.client.title") }} />
      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={styles.body} keyboardShouldPersistTaps="handled">
          <Text style={styles.heading}>{t("login.client.title")}</Text>
          <Input
            label={t("login.client.phone")}
            keyboardType="phone-pad"
            autoCapitalize="none"
            value={phone}
            onChangeText={setPhone}
          />
          <Input
            label={t("login.client.password")}
            secureTextEntry
            value={password}
            onChangeText={setPassword}
          />
          <View style={{ height: 8 }} />
          <Button title={t("login.client.submit")} onPress={submit} loading={loading} />

          <Pressable onPress={() => router.push("/register/client")} style={styles.linkWrap}>
            <Text style={styles.link}>
              {t("login.noAccount")} <Text style={styles.linkStrong}>{t("login.createAccount")}</Text>
            </Text>
          </Pressable>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: colors.bg },
  body: { padding: 20 },
  heading: { fontSize: 22, fontWeight: "700", color: colors.text, marginBottom: 16 },
  linkWrap: { paddingVertical: 18, alignItems: "center" },
  link: { color: colors.textMuted, fontSize: 14 },
  linkStrong: { color: colors.primary, fontWeight: "700" },
});
