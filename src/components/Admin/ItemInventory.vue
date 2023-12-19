
<template>
    <v-card>
        <v-tabs v-model="tab" color="deep-purple-accent-4" align-tabs="center">
            <v-tab :value="0">Inventory Items</v-tab>
            <v-tab :value="1">Borrowed Items</v-tab>
            <v-tab :value="2">Add new Item</v-tab>
        </v-tabs>
        <v-window v-model="tab">
            <v-window-item >
                <v-container fluid>
                  <div v-if="!isLoading && items.length === 0" style="display: flex; justify-content: center; font-size: 48px; color: gray;">
                    no items available in the inventory
                  </div>
                    <v-row>
                        <v-col v-for="(item, index) in items" :key="index" cols="12" md="4">
                            <v-card >
                                <v-img
                                :src="item.item_img"
                                :lazy-src="`https://picsum.photos/10/6?image=${index * (index+2) * 5 + 10}`"
                                cover
                                ></v-img>
                                <v-card-title class="text-capitalize">
                                  {{ item.item_name }}
                                </v-card-title>
                                <v-card-text>
                                  Quantity: {{ item.item_qty }}
                                    <v-spacer></v-spacer>
                                    Date added: {{ format(new Date(item.created_at), 'PPPP') }}
                                    <v-spacer></v-spacer>
                                    Last update:{{ format(new Date(item.updated_at), 'PPPP') }} 
                                </v-card-text>
                                <v-card-actions>
                                    <v-spacer/>
                                    <v-btn color="blue" prepend-icon="mdi-square-edit-outline" @click="showEditItem(item, index)">Update</v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-container>
            </v-window-item>
        </v-window>
  </v-card>

  <v-dialog v-model="editItemDialog" width="80%">
        <v-card title="Edit Item Quantity">
            <v-card-text>
                <v-text-field label="Item" readonly v-model="updateItemForm.item_name"></v-text-field>
                <v-text-field label="Quantity" type="number" min="0" v-model="updateItemForm.item_qty"></v-text-field>
            </v-card-text>
            <v-card-actions>
                <v-spacer/>
                <v-btn color="grey" @click="editItemDialog = false">Cancel</v-btn>
                <v-btn color="green" @click="updateItemQuantity" :loading="isLoading">Update</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-alert
      closable
      :title="alertMsg.title"
      :text="alertMsg.msg"
      :type="alertMsg.type"
      variant="tonal"
      class="text-capitalize"
    ></v-alert>
</template>

<script setup>
import {ref, onMounted } from 'vue';
import { format } from 'date-fns';
import $ from 'jquery';

const tab = ref(0);
const announcements = ref(null);
const editItemDialog = ref(false);
const deleteItemDialog = ref(false);
const api = 'http://localhost:4545/admin/router.php';
const isLoading = ref(false);
const items = ref([]);
const updateItemForm = ref({
  item_name: '',
  item_qty: null,
  item_id: null,
});
const alertMsg = ref({
  title: '',
  msg: '',
  type: null,
})

const getInventoryItems = async () => {
  try {
    isLoading.value = true;

    $.ajax({
      type: 'POST',
      url: api,
      data: {
        choice: 'get',
        item_id: 0
      },
      success: response => {
        items.value = JSON.parse(response);
      },
      error: error => {
        alertMsg.title = 'Error';
        alertMsg.type = 'error';
        alertMsg.msg = 'Something went wrong';
        throw error;
      }
    });
  } catch (error) {
    errMsg.value = error.responseText;
    throw error;
  } finally {
    isLoading.value = false
  }
}

const showEditItem = (item, index) => {
  try {
    editItemDialog.value = true;
    updateItemForm.value.item_name = item.item_name.toUpperCase();
    updateItemForm.value.item_id = item.item_id;

  } catch (error) {
    throw error;
  } finally {
    isLoading.value = false;
  }
}

const updateItemQuantity = () => {
  try {
    isLoading.value = true;

    $.ajax({
      type: 'POST',
      url: api,
      data: {
        choice: 'update',
        item_id: updateItemForm.value.item_id,
        item_qty: updateItemForm.value.item_qty
      },
      success: response => {
        const tmp = JSON.parse(response);

        const temps = items.value.find(item => item.item_id === updateItemForm.value.item_id);
        temps.item_qty = parseInt(temps.item_qty) + parseInt(updateItemForm.value.item_qty);
        temps.updated_at = Date.now();
        

        alertMsg.title = 'Updated';
        alertMsg.type = 'success';
        alertMsg.msg = tmp.message;

        editItemDialog.value = false;
        updateItemForm.value.item_qty = null;
      },
      error: error => {
        alertMsg.title = 'Error';
        alertMsg.type = 'error';
        alertMsg.msg = error.responseText;
        throw error;
      }
    })
  } catch (error) {
    alertMsg.title = 'Error';
    alertMsg.type = 'error';
    alertMsg.msg = error.responstText;

    throw error;
  } finally {
    isLoading.value = false;
    editItemDialog.value = false;
  }
}

onMounted(async () => {
  await getInventoryItems();
})
</script>
