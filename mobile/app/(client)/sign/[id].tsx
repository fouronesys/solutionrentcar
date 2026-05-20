import React, { useRef, useState } from "react";
import { Alert, StyleSheet, Text, View } from "react-native";
import { Stack, useLocalSearchParams, useRouter } from "expo-router";
import SignatureScreen, { SignatureViewRef } from "react-native-signature-canvas";
import { Button } from "@/components/Button";
import { api, ApiError } from "@/api/client";
import { colors } from "@/theme/colors";
import { t } from "@/i18n";

export default function SignBooking() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const ref = useRef<SignatureViewRef>(null);
  const [saving, setSaving] = useState(false);

  // The library calls onOK with the signature data URL once we ask for it.
  const onOK = async (signature: string) => {
    if (!signature) {
      Alert.alert(t("sign.missing"));
      setSaving(false);
      return;
    }
    try {
      await api.post(`/bookings/${id}/sign`, { signature });
      Alert.alert(t("sign.saved"));
      router.replace({ pathname: "/(client)/booking/[id]", params: { id: String(id) } });
    } catch (e) {
      Alert.alert(e instanceof ApiError ? e.message : t("common.error"));
    } finally {
      setSaving(false);
    }
  };

  const onEmpty = () => {
    Alert.alert(t("sign.missing"));
    setSaving(false);
  };

  const save = () => {
    setSaving(true);
    ref.current?.readSignature();
  };

  const clear = () => ref.current?.clearSignature();

  const webStyle = `
    .m-signature-pad { box-shadow: none; border: none; }
    .m-signature-pad--body { border: 2px dashed #cbd5e1; border-radius: 12px; background: #fff; }
    .m-signature-pad--footer { display: none; margin: 0; }
    body, html { background: ${colors.bg}; }
  `;

  return (
    <View style={styles.container}>
      <Stack.Screen options={{ headerShown: true, title: t("sign.title") }} />
      <Text style={styles.instructions}>{t("sign.instructions")}</Text>
      <View style={styles.pad}>
        <SignatureScreen
          ref={ref}
          onOK={onOK}
          onEmpty={onEmpty}
          webStyle={webStyle}
          backgroundColor="#ffffff"
          penColor="#0f172a"
          autoClear={false}
          descriptionText=""
        />
      </View>
      <View style={styles.actions}>
        <Button title={t("sign.clear")} variant="secondary" onPress={clear} style={{ flex: 1, marginRight: 6 }} />
        <Button title={t("sign.save")} onPress={save} loading={saving} style={{ flex: 2, marginLeft: 6 }} />
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.bg, padding: 16 },
  instructions: { color: colors.textMuted, marginBottom: 12, textAlign: "center" },
  pad: { flex: 1, borderRadius: 12, overflow: "hidden", backgroundColor: "#fff" },
  actions: { flexDirection: "row", marginTop: 12 },
});
