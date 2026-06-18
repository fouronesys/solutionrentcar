import React, { useState } from "react";
import { Alert, KeyboardAvoidingView, Platform, ScrollView, StyleSheet, Text, View } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { Stack } from "expo-router";
import { Button } from "@/components/Button";
import { Input } from "@/components/Input";
import { useAuth } from "@/auth/AuthContext";
import { ApiError } from "@/api/client";
import { colors } from "@/theme/colors";
import { t } from "@/i18n";

function loginErrorMessage(e: unknown): string {
  if (e instanceof ApiError) {
    switch (e.code) {
      case "network_unreachable":
        return t("login.errors.network");
      case "service_blocked":
        return t("login.errors.blocked");
      case "service_unavailable":
        return t("login.errors.unavailable");
      case "invalid_credentials":
        return t("login.errors.invalid");
      default:
        return e.message || t("login.errors.invalid");
    }
  }
  return t("login.errors.invalid");
}

export default function StaffLogin() {
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
    <SafeAreaView style={styles.safe}>
      <Stack.Screen options={{ headerShown: true, title: t("login.staff.title") }} />
      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={styles.body} keyboardShouldPersistTaps="handled">
          <Text style={styles.heading}>{t("login.staff.title")}</Text>
          <Input
            label={t("login.staff.user")}
            autoCapitalize="none"
            autoCorrect={false}
            value={user}
            onChangeText={setUser}
          />
          <Input label={t("login.staff.password")} secureTextEntry value={password} onChangeText={setPassword} />
          <View style={{ height: 8 }} />
          <Button title={t("login.staff.submit")} onPress={submit} loading={loading} />
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: colors.bg },
  body: { padding: 20 },
  heading: { fontSize: 22, fontWeight: "700", color: colors.text, marginBottom: 16 },
});
