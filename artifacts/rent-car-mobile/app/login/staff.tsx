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
import { colors, radius, type } from "@/theme/colors";
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

export default function StaffLogin() {
  const router = useRouter();
  const { loginStaff } = useAuth();
  const [user, setUser] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState(false);

  const submit = async () => {
    if (!user.trim() || !password.trim()) {
      Alert.alert(t("login.errors.empty"));
      return;
    }
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
      <StatusBar style="light" />
      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={styles.body} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>
          {/* Hero */}
          <View style={styles.hero}>
            <Pressable onPress={() => router.back()} style={styles.backBtn} hitSlop={8}>
              <Ionicons name="arrow-back" size={22} color="#FFFFFF" />
            </Pressable>
            <View style={styles.logoChip}>
              <Image source={require("../../assets/images/logo.png")} style={{ width: 82, height: 82 }} resizeMode="contain" />
            </View>
            <Text style={styles.brand}>SOLUTION</Text>
            <Text style={styles.brandAccent}>Staff</Text>
            <View style={styles.staffPill}>
              <Ionicons name="briefcase-outline" size={13} color="rgba(255,255,255,0.7)" />
              <Text style={styles.brandSub}>{t("login.staff.title")}</Text>
            </View>
          </View>

          {/* Card */}
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

            <Button title={t("login.staff.submit")} icon="log-in-outline" onPress={submit} loading={loading} size="lg" variant="dark" />

            <Pressable onPress={() => router.push("/(client)/cars")} style={styles.skipWrap}>
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
  screen: { flex: 1, backgroundColor: colors.dark },
  body: { flexGrow: 1 },
  hero: {
    alignItems: "center",
    paddingTop: 36,
    paddingBottom: 36,
    paddingHorizontal: 20,
  },
  backBtn: {
    position: "absolute",
    left: 16,
    top: 24,
    width: 42,
    height: 42,
    borderRadius: 21,
    backgroundColor: "rgba(255,255,255,0.08)",
    alignItems: "center",
    justifyContent: "center",
  },
  logoChip: {
    width: 90,
    height: 90,
    borderRadius: radius.xl,
    backgroundColor: "transparent",
    alignItems: "center",
    justifyContent: "center",
    marginBottom: 16,
    overflow: "hidden",
  },
  brand: { ...type.label, color: "rgba(255,255,255,0.6)", letterSpacing: 3 },
  brandAccent: { ...type.h1, color: colors.primary, marginTop: 2 },
  staffPill: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
    marginTop: 10,
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: radius.full,
    backgroundColor: "rgba(255,255,255,0.07)",
  },
  brandSub: { ...type.caption, color: "rgba(255,255,255,0.6)" },
  card: {
    flex: 1,
    backgroundColor: colors.bg,
    borderTopLeftRadius: radius.xxl,
    borderTopRightRadius: radius.xxl,
    padding: 20,
    paddingTop: 28,
    minHeight: 360,
  },
  title: { ...type.h1, color: colors.text, marginBottom: 24 },
  form: { marginBottom: 8 },
  skipWrap: { marginTop: 24, paddingVertical: 12, flexDirection: "row", alignItems: "center", justifyContent: "center", gap: 6 },
  skipText: { ...type.bodyMed, color: colors.textMuted },
});
