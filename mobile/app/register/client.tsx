/**
 * Registro de clientes — diseño limpio blanco + rojo.
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
import { useTheme, useThemedStyles } from "@/theme/ThemeContext";
import { t } from "@/i18n";

export default function ClientRegister() {
  const router = useRouter();
  const { registerClient } = useAuth();
  const [name, setName] = useState("");
  const [lastname, setLastname] = useState("");
  const [phone, setPhone] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [confirm, setConfirm] = useState("");
  const [loading, setLoading] = useState(false);
  const styles = useThemedStyles(makeStyles);
  const { isDark } = useTheme();

  const submit = async () => {
    if (!name.trim() || !phone.trim() || !password) { Alert.alert(t("register.errors.required")); return; }
    if (password.length < 6) { Alert.alert(t("register.errors.passwordShort")); return; }
    if (password !== confirm) { Alert.alert(t("register.errors.passwordMismatch")); return; }
    setLoading(true);
    try {
      await registerClient({
        name: name.trim(),
        lastname: lastname.trim() || undefined,
        phone: phone.trim(),
        email: email.trim() || undefined,
        password,
      });
    } catch (e) {
      Alert.alert(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.screen} edges={["top"]}>
      <Stack.Screen options={{ headerShown: false }} />
      <StatusBar style={isDark ? "light" : "dark"} />
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

          {/* Brand */}
          <View style={styles.brandBlock}>
            <View style={styles.logoWrap}>
              <Image
                source={require("../../assets/branding/casa-rivas-rent-car-logo.png")}
                style={{ width: 80, height: 80 }}
                resizeMode="contain"
              />
            </View>
            <Text style={styles.brand}>CASA RIVAS</Text>
            <Text style={styles.brandSub}>Rent-Car</Text>
          </View>

          {/* Form card */}
          <View style={styles.card}>
            <Text style={styles.title}>{t("register.title")}</Text>
            <Text style={styles.subtitle}>{t("register.subtitle")}</Text>

            <View style={styles.nameRow}>
              <View style={{ flex: 1, marginRight: 8 }}>
                <Input label={t("register.name")} icon="person-outline" value={name} onChangeText={setName} autoCapitalize="words" />
              </View>
              <View style={{ flex: 1 }}>
                <Input label={t("register.lastname")} icon="people-outline" value={lastname} onChangeText={setLastname} autoCapitalize="words" />
              </View>
            </View>
            <Input
              label={t("register.phone")}
              icon="call-outline"
              value={phone}
              onChangeText={setPhone}
              keyboardType="phone-pad"
              autoCapitalize="none"
              placeholder="809-000-0000"
            />
            <Input
              label={t("register.email")}
              icon="mail-outline"
              value={email}
              onChangeText={setEmail}
              keyboardType="email-address"
              autoCapitalize="none"
              placeholder={`${t("register.email")} (opcional)`}
            />
            <Input
              label={t("register.password")}
              icon="lock-closed-outline"
              value={password}
              onChangeText={setPassword}
              secureTextEntry
              placeholder="Mín. 6 caracteres"
            />
            <Input
              label={t("register.passwordConfirm")}
              icon="lock-closed-outline"
              value={confirm}
              onChangeText={setConfirm}
              secureTextEntry
              placeholder="Repite la contraseña"
            />

            <Button
              title={t("register.submit")}
              icon="person-add-outline"
              onPress={submit}
              loading={loading}
              size="lg"
              style={{ marginTop: 8 }}
            />

            <View style={styles.footer}>
              <Text style={styles.footerText}>{t("register.haveAccount")} </Text>
              <Pressable onPress={() => router.replace("/login/client")}>
                <Text style={styles.footerLink}>{t("register.signIn")}</Text>
              </Pressable>
            </View>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const makeStyles = () => StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.bg },
  body: { flexGrow: 1, padding: 20 },

  backBtn: {
    width: 42, height: 42, borderRadius: 21,
    backgroundColor: colors.card,
    alignItems: "center", justifyContent: "center",
    ...shadow.xs,
    marginBottom: 8,
  },

  brandBlock: { alignItems: "center", paddingVertical: 28 },
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
  title: { ...type.h1, color: colors.text, marginBottom: 4 },
  subtitle: { ...type.callout, color: colors.textMuted, marginBottom: 24 },
  nameRow: { flexDirection: "row" },
  footer: {
    flexDirection: "row",
    justifyContent: "center",
    alignItems: "center",
    paddingVertical: 24,
  },
  footerText: { ...type.callout, color: colors.textMuted },
  footerLink: { ...type.callout, color: colors.cta, fontFamily: font.bold },
});
