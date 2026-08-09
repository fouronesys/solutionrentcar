/**
 * Login de staff — diseño limpio blanco + oscuro.
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

export default function StaffLogin() {
  const router = useRouter();
  const { loginStaff } = useAuth();
  const [user, setUser] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState(false);

  const submit = async () => {
    if (!user.trim() || !password.trim()) { Alert.alert(t("login.errors.empty")); return; }
    setLoading(true);
    try {
      await loginStaff(user.trim(), password);
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

          {/* Brand block */}
          <View style={styles.brandBlock}>
            <View style={styles.logoWrap}>
              <Image
                source={require("../../assets/images/logo.png")}
                style={{ width: 80, height: 80 }}
                resizeMode="contain"
              />
            </View>
            <Text style={styles.brand}>YOWELL</Text>
            <Text style={styles.brandSub}>Equipo</Text>
            <View style={styles.staffPill}>
              <Ionicons name="briefcase-outline" size={13} color={colors.textSecondary} />
              <Text style={styles.staffPillText}>{t("login.staff.title")}</Text>
            </View>
          </View>

          {/* Form card */}
          <View style={styles.card}>
            <Text style={styles.title}>{t("login.staff.title")}</Text>

            <View style={styles.form}>
              <Input
                label={t("login.staff.user")}
                icon="person-outline"
                autoCapitalize="none"
                autoCorrect={false}
                autoComplete="username"
                value={user}
                onChangeText={setUser}
                placeholder="usuario"
              />
              <Input
                label={t("login.staff.password")}
                icon="lock-closed-outline"
                secureTextEntry
                autoComplete="password"
                value={password}
                onChangeText={setPassword}
                placeholder="••••••••"
              />
            </View>

            <Button
              title={t("login.staff.submit")}
              icon="log-in-outline"
              onPress={submit}
              loading={loading}
              size="lg"
              variant="dark"
            />

            <Pressable
              onPress={() => router.push("/(client)/cars")}
              style={styles.skipWrap}
            >
              <Ionicons name="car-sport-outline" size={16} color={colors.textMuted} />
              <Text style={styles.skipText}>{t("cars.title")}</Text>
            </Pressable>
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
  brandSub: { ...type.h2, color: colors.text, marginBottom: 10 },
  staffPill: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
    paddingHorizontal: 14,
    paddingVertical: 7,
    backgroundColor: colors.card,
    borderRadius: radius.full,
    borderWidth: 1,
    borderColor: colors.border,
  },
  staffPillText: { ...type.captionMed, color: colors.textSecondary },

  card: {
    backgroundColor: colors.card,
    borderRadius: radius.xxl,
    padding: 24,
    paddingTop: 28,
    ...shadow.lg,
  },
  title: { ...type.h1, color: colors.text, marginBottom: 24 },
  form: { marginBottom: 8 },
  skipWrap: {
    marginTop: 24,
    paddingVertical: 12,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 6,
  },
  skipText: { ...type.bodyMed, color: colors.textMuted },
});
