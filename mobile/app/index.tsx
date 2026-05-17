import React from "react";
import { Image, StyleSheet, Text, View } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from "expo-router";
import { Button } from "@/components/Button";
import { colors } from "@/theme/colors";
import { t } from "@/i18n";

export default function Welcome() {
  const router = useRouter();
  return (
    <SafeAreaView style={styles.safe}>
      <View style={styles.hero}>
        <Image source={require("../assets/icon.png")} style={styles.logo} />
        <Text style={styles.title}>{t("welcome.title")}</Text>
        <Text style={styles.sub}>{t("welcome.subtitle")}</Text>
      </View>
      <View style={styles.actions}>
        <Button title={t("welcome.asClient")} onPress={() => router.push("/login/client")} />
        <View style={{ height: 12 }} />
        <Button
          title={t("welcome.asStaff")}
          variant="secondary"
          onPress={() => router.push("/login/staff")}
        />
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: colors.primary },
  hero: { flex: 1, alignItems: "center", justifyContent: "center", padding: 24 },
  logo: { width: 140, height: 140, borderRadius: 28, marginBottom: 24 },
  title: { color: "#fff", fontSize: 28, fontWeight: "700", textAlign: "center" },
  sub: { color: "#cbd5e1", fontSize: 15, marginTop: 8, textAlign: "center" },
  actions: { padding: 24, backgroundColor: colors.primary },
});
