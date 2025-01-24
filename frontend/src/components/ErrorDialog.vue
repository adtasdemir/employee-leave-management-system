<template>
    <v-dialog v-model="internalShow" max-width="500px">
      <v-card>
        <v-card-title>
          <span class="headline">
            <strong style="color: red">Error</strong>
          </span>
        </v-card-title>
        <v-card-text>
          {{ errorMessage }}
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text color="red" @click="closeDialog">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </template>
  
  <script>
  
  export default {
    name: "ErrorDialog",
    props: {
      modelValue: {
        type: Boolean,
        required: true,
      },
      errorMessage: {
        type: String,
        default: "",
      },
    },
    computed: {
      internalShow: {
        get() {
          return this.modelValue;
        },
        set(value) {
          this.$emit("update:modelValue", value);
        },
      },
    },
    methods: {
      closeDialog() {
        this.internalShow = false;
      },
    },
  };
  </script>
  