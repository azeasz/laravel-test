import React, { useState } from 'react';
import { View, Text, TextInput, Button, Modal, FlatList, CheckBox, StyleSheet } from 'react-native';
import axios from 'axios';

const FobiUpload = () => {
  const [faunas, setFaunas] = useState<Array<{ name: string; id: number | null; count: number; notes: string; breeding: boolean }>>([]);
  const [modalVisible, setModalVisible] = useState(false);
  const [newFauna, setNewFauna] = useState({
    name: '',
    id: null,
    count: 0,
    notes: '',
    breeding: false
  });

  const showModal = () => setModalVisible(true);
  const closeModal = () => setModalVisible(false);

  const addFauna = () => {
    setFaunas([...faunas, { ...newFauna }]);
    setNewFauna({ name: '', id: null, count: 0, notes: '', breeding: false });
    closeModal();
  };

  const fetchFaunaId = () => {
    if (newFauna.name.length > 2) {
      axios.get('http://localhost:8000/api/faunas', { params: { name: newFauna.name } })
        .then(response => {
          setNewFauna({ ...newFauna, id: response.data.fauna_id });
        })
        .catch(error => {
          console.error(error);
        });
    }
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Upload Fobi Data</Text>
      <TextInput placeholder="Latitude" style={styles.input} />
      <TextInput placeholder="Longitude" style={styles.input} />
      <TextInput placeholder="Tujuan Pengamatan" style={styles.input} keyboardType="numeric" />
      <TextInput placeholder="Observer" style={styles.input} />
      <TextInput placeholder="Additional Note" style={styles.input} />
      <TextInput placeholder="Active" style={styles.input} keyboardType="numeric" />
      <TextInput placeholder="Tanggal Pengamatan" style={styles.input} />
      <TextInput placeholder="Start Time" style={styles.input} />
      <TextInput placeholder="End Time" style={styles.input} />
      <TextInput placeholder="Completed" style={styles.input} keyboardType="numeric" />

      <Text style={styles.subtitle}>Daftar Burung</Text>
      <FlatList
        data={faunas}
        keyExtractor={(item) => item.name}
        renderItem={({ item }) => (
          <Text>{item.id} - {item.name} - {item.count} - {item.breeding ? 'Ya' : 'Tidak'}</Text>
        )}
      />

      <Button title="Tambah Jenis" onPress={showModal} />
      <Button title="Upload Data" onPress={() => { /* Handle upload */ }} />

      <Modal visible={modalVisible} animationType="slide">
        <View style={styles.modalContent}>
          <Text>Tambah Jenis Burung</Text>
          <TextInput
            placeholder="Jenis burung"
            style={styles.input}
            value={newFauna.name}
            onChangeText={(text) => setNewFauna({ ...newFauna, name: text })}
            onBlur={fetchFaunaId}
          />
          <TextInput
            placeholder="Jumlah individu"
            style={styles.input}
            keyboardType="numeric"
            value={String(newFauna.count)}
            onChangeText={(text) => setNewFauna({ ...newFauna, count: parseInt(text) })}
          />
          <TextInput
            placeholder="Catatan"
            style={styles.input}
            value={newFauna.notes}
            onChangeText={(text) => setNewFauna({ ...newFauna, notes: text })}
          />
          <View style={styles.checkboxContainer}>
            <CheckBox
              value={newFauna.breeding}
              onValueChange={(value) => setNewFauna({ ...newFauna, breeding: value })}
            />
            <Text>Apakah berbiak?</Text>
          </View>
          <Button title="Simpan" onPress={addFauna} />
          <Button title="Tutup" onPress={closeModal} />
        </View>
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    padding: 20,
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 20,
  },
  subtitle: {
    fontSize: 18,
    marginVertical: 10,
  },
  input: {
    borderWidth: 1,
    borderColor: '#ccc',
    padding: 10,
    marginVertical: 5,
    color: 'black'
  },
  modalContent: {
    padding: 20,
  },
  checkboxContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginVertical: 10,
  },
});

export default FobiUpload;