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
      await registerClient({ name: name.trim(), lastname: lastname.trim(), phone: phone.trim(), email: email.trim(), password });
    } catch (e) {
      Alert.alert(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.safe}>
      <Stack.Screen options={{ headerShown: true, title: t("register.title") }} />
      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={styles.body} keyboardShouldPersistTaps="handled">
          <Text style={styles.heading}>{t("register.title")}</Text>
          <Text style={styles.subtitle}>{t("register.subtitle")}</Text>
          <Input label={t("register.name")} value={name} onChangeText={setName} autoCapitalize="words" />
          <Input label={t("register.lastname")} value={lastname} onChangeText={setLastname} autoCapitalize="words" />
          <Input label={t("register.phone")} value={phone} onChangeText={setPhone} keyboardType="phone-pad" autoCapitalize="none" />
          <Input label={t("register.email")} value={email} onChangeText={setEmail} keyboardType="email-address" autoCapitalize="none" />
          <Input label={t("register.password")} value={password} onChangeText={setPassword} secureTextEntry />
          <Input label={t("register.passwordConfirm")} value={confirm} onChangeText={setConfirm} secureTextEntry />
          <View style={{ height: 12 }} />
          <Button title={t("register.submit")} onPress={submit} loading={loading} />
          <Pressable onPress={() => router.replace("/login/client")} style={styles.linkWrap}>
            <Text style={styles.link}>
              {t("register.haveAccount")} <Text style={styles.linkStrong}>{t("register.signIn")}</Text>
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
  heading: { fontSize: 22, fontWeight: "700", color: colors.text, marginBottom: 4 },
  subtitle: { color: colors.textMuted, marginBottom: 16 },
  linkWrap: { paddingVertical: 16, alignItems: "center" },
  link: { color: colors.textMuted, fontSize: 14 },
  linkStrong: { color: colors.primaryDark, fontWeight: "700" },
});
