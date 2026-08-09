/**
 * Login de clientes — diseño limpio blanco + rojo.
 */
import React, { useState } from "react";
import {
  Alert,
  Image,
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
import { Ionicons } from "@expo/vector-icons";
import { StatusBar } from "expo-status-bar";
import { Button } from "@/components/Button";
import { Input } from "@/components/Input";
import { useAuth } from "@/auth/AuthContext";
import { ApiError } from "@/api/client";
import { colors, font, radius, shadow, type } from "@/theme/colors";
import { t } from "@/i18n";

function loginErrorMessage(e: unknown): string {
  if (e instanceof ApiError) {
    switch (e.code) {
      case "network_unreachable": return t("login.errors.network");
      case "service_blocked":     return t("login.errors.blocked");
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
    if (!phone.trim() || !password.trim()) { Alert.alert(t("login.errors.empty")); return; }
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
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <Stack.Screen options={{ headerShown: false }} />
      <StatusBar style="dark" />
      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView
          contentContainerStyle={styles.body}
          keyboardShouldPersistTaps="handled"
          showsVerticalScrollIndicator={false}
        >
          {/* Back */}
          <Pressable onPress={() => router.back()} style={styles.backBtn} hitSlop={8}>
            <Ionicons name="arrow-back" size={22} color={colors.text} />
          </Pressable>

          {/* Logo + brand */}
          <View style={styles.brandBlock}>
            <View style={styles.logoWrap}>
              <Image
                source={require("../../assets/images/logo.png")}
                style={{ width: 80, height: 80 }}
                resizeMode="contain"
              />
            </View>
            <Text style={styles.brand}>YOWELL</Text>
            <Text style={styles.brandSub}>Rent-Car</Text>
          </View>

          {/* Form card */}
          <View style={styles.card}>
            <Text style={styles.title}>{t("login.client.title")}</Text>
            <Text style={styles.subtitle}>{t("login.requiredSubtitle")}</Text>

            <View style={styles.form}>
              <Input
                label={t("login.client.phone")}
                icon="call-outline"
                keyboardType="phone-pad"
                autoCapitalize="none"
                autoComplete="tel"
                value={phone}
                onChangeText={setPhone}
                placeholder="809-000-0000"
              />
              <Input
                label={t("login.client.password")}
                icon="lock-closed-outline"
                secureTextEntry
                autoComplete="password"
                value={password}
                onChangeText={setPassword}
                placeholder="••••••••"
              />
            </View>

            <Button
              title={t("login.client.submit")}
              icon="log-in-outline"
              onPress={submit}
              loading={loading}
              size="lg"
            />

            <View style={styles.divider}>
              <View style={styles.dividerLine} />
              <Text style={styles.dividerText}>{t("login.noAccount")}</Text>
              <View style={styles.dividerLine} />
            </View>

            <Button
              title={t("login.createAccount")}
              variant="secondary"
              icon="person-add-outline"
              onPress={() => router.push("/register/client")}
              size="lg"
            />
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  body: { flexGrow: 1, padding: 20 },

  backBtn: {
    width: 42, height: 42, borderRadius: 21,
    backgroundColor: colors.card,
    alignItems: "center", justifyContent: "center",
    ...shadow.xs,
    marginBottom: 8,
  },

  brandBlock: { alignItems: "center", paddingVertical: 32 },
  logoWrap: {
    width: 96, height: 96,
    borderRadius: radius.xl,
    backgroundColor: colors.card,
    alignItems: "center", justifyContent: "center",
    marginBottom: 14,
    ...shadow.md,
    overflow: "hidden",
  },
  brand: { ...type.label, color: colors.textSecondary, letterSpacing: 3, marginBottom: 4 },
  brandSub: { ...type.h2, color: colors.text },

  card: {
    backgroundColor: colors.card,
    borderRadius: radius.xxl,
    padding: 24,
    paddingTop: 28,
    ...shadow.lg,
  },
  title: { ...type.h1, color: colors.text, marginBottom: 6 },
  subtitle: { ...type.callout, color: colors.textMuted, marginBottom: 24 },
  form: { marginBottom: 8 },
  divider: { flexDirection: "row", alignItems: "center", marginVertical: 20 },
  dividerLine: { flex: 1, height: 1, backgroundColor: colors.border },
  dividerText: { marginHorizontal: 12, ...type.small, color: colors.textMuted },
});
