import React, { useState } from "react";
import {
  Alert,
  KeyboardAvoidingView,
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { Stack, useRouter } from "expo-router";
import { Button } from "@/components/Button";
import { Input } from "@/components/Input";
import { useAuth } from "@/auth/AuthContext";
import { ApiError } from "@/api/client";
import { colors, radius, shadow, spacing } from "@/theme/colors";
import { t } from "@/i18n";

function loginErrorMessage(e: unknown): string {
  if (e instanceof ApiError) {
    switch (e.code) {
      case "network_unreachable": return t("login.errors.network");
      case "service_blocked": return t("login.errors.blocked");
      case "service_unavailable": return t("login.errors.unavailable");
      case "invalid_credentials": return t("login.errors.invalid");
      default: return e.message || t("login.errors.invalid");
    }
  }
  return t("login.errors.invalid");
}

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
      Alert.alert(loginErrorMessage(e));
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.screen}>
      <Stack.Screen options={{ headerShown: false }} />
      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={styles.body} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>
          {/* Hero */}
          <View style={styles.hero}>
            <Text style={styles.logo}>🚗</Text>
            <Text style={styles.brand}>Solutions</Text>
            <Text style={styles.brandAccent}>Rent Car</Text>
          </View>

          {/* Card */}
          <View style={styles.card}>
            <Text style={styles.title}>{t("login.client.title")}</Text>
            <Text style={styles.subtitle}>{t("login.requiredSubtitle")}</Text>

            <View style={styles.form}>
              <Input
                label={t("login.client.phone")}
                keyboardType="phone-pad"
                autoCapitalize="none"
                autoComplete="tel"
                value={phone}
                onChangeText={setPhone}
                placeholder="809-000-0000"
              />
              <Input
                label={t("login.client.password")}
                secureTextEntry
                autoComplete="password"
                value={password}
                onChangeText={setPassword}
                placeholder="••••••••"
              />
            </View>

            <Button title={t("login.client.submit")} onPress={submit} loading={loading} size="lg" />

            <View style={styles.divider}>
              <View style={styles.dividerLine} />
              <Text style={styles.dividerText}>{t("login.noAccount")}</Text>
              <View style={styles.dividerLine} />
            </View>

            <Button
              title={t("login.createAccount")}
              variant="secondary"
              onPress={() => router.push("/register/client")}
              size="lg"
            />
          </View>

          <Pressable onPress={() => router.back()} style={styles.skipWrap}>
            <Text style={styles.skipText}>← {t("common.back")}</Text>
          </Pressable>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.dark },
  body: { flexGrow: 1 },
  hero: {
    alignItems: "center",
    paddingTop: 48,
    paddingBottom: 32,
    backgroundColor: colors.dark,
  },
  logo: { fontSize: 48, marginBottom: 12 },
  brand: { color: "rgba(255,255,255,0.7)", fontSize: 13, fontWeight: "700", letterSpacing: 2, textTransform: "uppercase" },
  brandAccent: { color: colors.primary, fontSize: 28, fontWeight: "800", letterSpacing: -0.3 },
  card: {
    flex: 1,
    backgroundColor: colors.bg,
    borderTopLeftRadius: 32,
    borderTopRightRadius: 32,
    padding: spacing.xl,
    paddingTop: spacing.xxl,
    minHeight: 400,
  },
  title: { fontSize: 24, fontWeight: "800", color: colors.text, marginBottom: 6 },
  subtitle: { fontSize: 14, color: colors.textMuted, marginBottom: 28, lineHeight: 20 },
  form: { marginBottom: 8 },
  divider: { flexDirection: "row", alignItems: "center", marginVertical: 20 },
  dividerLine: { flex: 1, height: 1, backgroundColor: colors.border },
  dividerText: { marginHorizontal: 12, fontSize: 12, color: colors.textMuted, fontWeight: "600" },
  skipWrap: { paddingVertical: 20, alignItems: "center", backgroundColor: colors.bg },
  skipText: { color: colors.textMuted, fontSize: 14, fontWeight: "600" },
});
