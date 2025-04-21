<template>
    <div v-if="show" class="modal">
        <div class="modal-content">
            <span class="close" @click="closeModal">&times;</span>
            <h2>Tambah Jenis Burung</h2>
            <form @submit.prevent="addFauna">
                <input type="text" v-model="fauna.name" placeholder="Jenis burung" required>
                <input type="number" v-model="fauna.count" placeholder="Jumlah individu" required>
                <input type="text" v-model="fauna.notes" placeholder="Catatan">
                <label>
                    Apakah berbiak?
                    <input type="checkbox" v-model="fauna.breeding">
                </label>
                <button type="submit">Simpan</button>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            show: false,
            fauna: {
                name: '',
                count: 0,
                notes: '',
                breeding: false
            }
        };
    },
    methods: {
        closeModal() {
            this.show = false;
        },
        addFauna() {
            this.$emit('add-fauna', { ...this.fauna });
            this.closeModal();
        }
    }
};
</script>

<style>
.modal {
    display: block;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>
