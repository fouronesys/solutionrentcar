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
import { colors, spacing } from "@/theme/colors";
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

  const submit = async () => {
    if (!name.trim() || !phone.trim() || !password) {
      Alert.alert(t("register.errors.required"));
      return;
    }
    if (password.length < 6) {
      Alert.alert(t("register.errors.passwordShort"));
      return;
    }
    if (password !== confirm) {
      Alert.alert(t("register.errors.passwordMismatch"));
      return;
    }
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
    <SafeAreaView style={styles.screen}>
      <Stack.Screen options={{ headerShown: false }} />
      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={styles.body} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>
          <View style={styles.hero}>
            <Text style={styles.logo}>🚀</Text>
            <Text style={styles.brand}>Solutions</Text>
            <Text style={styles.brandAccent}>Rent Car</Text>
          </View>

          <View style={styles.card}>
            <Text style={styles.title}>{t("register.title")}</Text>
            <Text style={styles.subtitle}>{t("register.subtitle")}</Text>

            <View style={styles.nameRow}>
              <View style={{ flex: 1, marginRight: 8 }}>
                <Input label={t("register.name")} value={name} onChangeText={setName} autoCapitalize="words" />
              </View>
              <View style={{ flex: 1 }}>
                <Input label={t("register.lastname")} value={lastname} onChangeText={setLastname} autoCapitalize="words" />
              </View>
            </View>
            <Input
              label={t("register.phone")}
              value={phone}
              onChangeText={setPhone}
              keyboardType="phone-pad"
              autoCapitalize="none"
              placeholder="809-000-0000"
            />
            <Input
              label={t("register.email")}
              value={email}
              onChangeText={setEmail}
              keyboardType="email-address"
              autoCapitalize="none"
              placeholder={`${t("register.email")} (${t("common.ok").toLowerCase()})`}
            />
            <Input label={t("register.password")} value={password} onChangeText={setPassword} secureTextEntry placeholder="Mín. 6 caracteres" />
            <Input label={t("register.passwordConfirm")} value={confirm} onChangeText={setConfirm} secureTextEntry placeholder="Repite la contraseña" />

            <Button title={t("register.submit")} onPress={submit} loading={loading} size="lg" style={{ marginTop: 8 }} />

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

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.dark },
  body: { flexGrow: 1 },
  hero: {
    alignItems: "center",
    paddingTop: 40,
    paddingBottom: 28,
    backgroundColor: colors.dark,
  },
  logo: { fontSize: 42, marginBottom: 10 },
  brand: { color: "rgba(255,255,255,0.7)", fontSize: 13, fontWeight: "700", letterSpacing: 2, textTransform: "uppercase" },
  brandAccent: { color: colors.primary, fontSize: 26, fontWeight: "800", letterSpacing: -0.3 },
  card: {
    flex: 1,
    backgroundColor: colors.bg,
    borderTopLeftRadius: 32,
    borderTopRightRadius: 32,
    padding: spacing.xl,
    paddingTop: spacing.xxl,
  },
  title: { fontSize: 24, fontWeight: "800", color: colors.text, marginBottom: 4 },
  subtitle: { fontSize: 14, color: colors.textMuted, marginBottom: 24, lineHeight: 20 },
  nameRow: { flexDirection: "row" },
  footer: { flexDirection: "row", justifyContent: "center", paddingVertical: 24 },
  footerText: { color: colors.textMuted, fontSize: 14 },
  footerLink: { color: colors.primaryDark, fontWeight: "700", fontSize: 14 },
});
