<script>
import {
  BCard,
  BRow,
  BCol,
  BFormInput,
  BButton,
  BLink,
  BDropdown,
  BDropdownItem,
  BPagination,
  BSpinner,
  BOverlay,
  BFormGroup,
  BCardText,
  BTable,
  BForm,
} from "bootstrap-vue";
import vSelect from "vue-select";

import dayjs from "dayjs";
import "dayjs/locale/th";
import buddhistEra from "dayjs/plugin/buddhistEra";
dayjs.extend(buddhistEra);

import {
  ref,
  watchEffect,
  reactive,
  onUnmounted,
  computed,
} from "@vue/composition-api";
import store from "@/store";
import fixStoreModule from "./fixStoreModule";
import { useToast } from "vue-toastification/composition";
import ToastificationContent from "@core/components/toastification/ToastificationContent.vue";
import Swal from "sweetalert2";
import { ValidationProvider, ValidationObserver } from "vee-validate";
import { required } from "@validations";
import { getUserData } from "@/auth/utils";

export default {
  components: {
    BCard,
    BRow,
    BCol,
    BFormInput,
    BButton,
    BLink,
    BDropdown,
    BDropdownItem,
    BPagination,
    BSpinner,
    BOverlay,
    vSelect,
    BFormGroup,
    BPagination,
    BCardText,
    dayjs,
    BTable,
    BForm,
    ValidationProvider,
    ValidationObserver,
    required,
  },
  setup() {
    const FIX_APP_STORE_MODULE_NAME = "fix-list";

    // Register module
    if (!store.hasModule(FIX_APP_STORE_MODULE_NAME))
      store.registerModule(FIX_APP_STORE_MODULE_NAME, fixStoreModule);

    onUnmounted(() => {});

    const toast = useToast();

    const errorToast = (message) => {
      toast({
        component: ToastificationContent,
        props: {
          title: "Error : " + message,
          icon: "AlertTriangleIcon",
          variant: "danger",
        },
      });
    };

    const isAdmin = getUserData().type == "admin" ? true : false;

    const items = ref([]);

    const isOverLay = ref(false);
    const simpleRules = ref();

    const perPage = ref({ title: "10", code: 10 });
    const currentPage = ref(1);
    const totalPage = ref(1);
    const totalItems = ref(0);
    const orderBy = ref({
      title: "วันที่แจ้ง",
      code: "fix_date",
    });
    const order = ref({ title: "DESC", code: "desc" });

    const fields = reactive([
      {
        key: "id",
        label: "Id",
        visible: false,
      },
      {
        key: "title",
        label: "หัวข้อแจ้งซ่อม",
        sortable: true,
        visible: true,
        class: "text-center",
        tdClass: "mw-3-5",
      },
      {
        key: "place",
        label: "สถานที่",
        sortable: true,
        visible: true,
        class: "text-center",
        tdClass: "mw-3-5",
      },
      {
        key: "fix_date",
        label: "วันที่แจ้ง",
        sortable: true,
        visible: true,
        class: "text-center",
        tdClass: "mw-3-5",
      },
      {
        key: "name",
        label: "ผู้แจ้ง",
        sortable: true,
        visible: true,
        class: "text-center",
        tdClass: "mw-3-5",
      },
      {
        key: "status",
        label: "สถานะ",
        sortable: true,
        visible: true,
        class: "text-center",
        tdClass: "mw-3-5",
      },
      {
        key: "email",
        label: "เมล",
        sortable: true,
        visible: false,
        class: "text-center",
        tdClass: "mw-3-7",
      },
      {
        key: "action",
        label: "ดูข้อมูล",
        visible: true,
        class: "text-center",
        tdClass: "mw-8",
      },
    ]);

    const visibleFields = computed(() => fields.filter((f) => f.visible));

    const item = ref({
      title: "",
      place: "",
      detail: "",
      fix_date: dayjs().format("YYYY-MM-DD"),
      name: "",
    });

    const statusList = {
      1: "อยู่ระหว่างการตรวจสอบ",
      2: "ส่งต่อเรื่องให้ผู้รับผิดชอบ",
      3: "รอใบสั่งจ้าง",
      4: "อยู่ระหว่างดำเนินการซ่อมแซม",
      5: "ดำเนินการแล้วเสร็จ",
    };

    const selectOptions = ref({
      perPage: [
        { title: "1", code: 1 },
        { title: "2", code: 2 },
        { title: "10", code: 10 },
        { title: "20", code: 20 },
        { title: "50", code: 50 },
      ],
      orderBy: [
        { title: "วันที่แจ้ง", code: "fix_date" },
        { title: "หัวข่้อแจ้งซ่อม", code: "title" },
      ],
      order: [
        { title: "ASC", code: "asc" },
        { title: "DESC", code: "desc" },
      ],
      statuses: [
        { title: "อยู่ระหว่างการตรวจสอบ", code: 1 },
        { title: "ส่งต่อเรื่องให้ผู้รับผิดชอบ", code: 2 },
        { title: "รอใบสั่งจ้าง", code: 3 },
        { title: "อยู่ระหว่างดำเนินการซ่อมแซม", code: 4 },
        { title: "ดำเนินการแล้วเสร็จ", code: 5 },
      ],
    });

    const fetchItems = () => {
      isOverLay.value = true;
      store
        .dispatch("fix-list/fetchFixes", {
          perPage: perPage.value.code,
          currentPage: currentPage.value == 0 ? undefined : currentPage.value,
          orderBy: orderBy.value.code,
          order: order.value.code,
          user_id: isAdmin ? undefined : getUserData().userID,
        })
        .then((response) => {
          items.value = response.data.data;
          totalPage.value = response.data.totalPage;
          totalItems.value = response.data.totalData;
          isOverLay.value = false;
        })
        .catch((error) => {
          console.log(error);
          toast({
            component: ToastificationContent,
            props: {
              title: "Error fetching Fix's list",
              icon: "AlertTriangleIcon",
              variant: "danger",
            },
          });
          isOverLay.value = false;
        });
    };
    fetchItems();

    watchEffect(() => {
      fetchItems();
    });

    const onChangePage = (page) => {
      currentPage.value = page;
    };

    //
    const onConfirmDelete = (id) => {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
        customClass: {
          confirmButton: "btn btn-primary",
          cancelButton: "btn btn-outline-danger ml-1",
        },
        buttonsStyling: false,
      }).then((result) => {
        if (result.isConfirmed) {
          onDelete(id);
          Swal.fire({
            icon: "success",
            title: "Deleted!",
            text: "Your file has been deleted.",
            customClass: {
              confirmButton: "btn btn-success",
            },
          });
        }
      });
    };

    const onDelete = (id) => {
      store
        .dispatch("fix-list/deleteFix", { id: id })
        .then((response) => {
          if (response.data.message == "success") {
            toast({
              component: ToastificationContent,
              props: {
                title: "Success : Deleted Fix",
                icon: "CheckIcon",
                variant: "success",
              },
            });
            fetchItems();
          } else {
            console.log("error");
          }
        })
        .catch((error) => {
          let textErrors = "";
          Object.values(error.response.data.errors).forEach((textError) => {
            textErrors = textErrors + textError + "<br>";
          });
          errorToast(textErrors);
        });
    };

    return {
      items,
      item,
      isOverLay,
      perPage,
      currentPage,
      totalPage,
      totalItems,
      orderBy,
      order,
      selectOptions,
      onChangePage,
      visibleFields,
      onConfirmDelete,
      simpleRules,
      isOverLay,
      isAdmin,
      dayjs,
      statusList,
    };
  },
};
</script>

<style></style>

<template>
  <div class="container-lg">
    <b-card no-body>
      <b-overlay :show="isOverLay" opacity="0.3" spinner-variant="primary">
        <div class="m-2">
          <b-row>
            <b-col>
              <b-form-group class="float-left col-lg-2">
                <v-select
                  v-model="perPage"
                  :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                  label="title"
                  :options="selectOptions.perPage"
                  :clearable="false"
                />
              </b-form-group>
              <b-form-group class="float-left col-lg-4">
                <v-select
                  v-model="orderBy"
                  :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                  label="title"
                  :options="selectOptions.orderBy"
                  :clearable="false"
                />
              </b-form-group>
              <b-form-group class="float-left col-lg-2">
                <v-select
                  v-model="order"
                  :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                  label="title"
                  :options="selectOptions.order"
                  :clearable="false"
                />
              </b-form-group>

              <b-button
                variant="outline-success"
                @click="
                  $router.push({
                    name: 'fix-add',
                  })
                "
                class="float-right"
              >
                <feather-icon icon="PlusIcon" />
                ADD
              </b-button>
            </b-col>
          </b-row>
          <hr />
        </div>

        <!-- List -->
        <div class="m-2">
          <b-row>
            <!-- Table -->
            <b-col cols="12">
              <b-table
                striped
                bordered
                hover
                responsive
                :items="items"
                :fields="visibleFields"
              >
                <template #cell(fix_date)="row">
                  {{ dayjs(row.item.fix_date).locale("th").format("DD/MM/BB") }}
                </template>
                <template #cell(status)="row">
                  {{ statusList[row.item.status] }}
                </template>
                <template #cell(action)="row">
                  <b-button
                    variant="outline-success"
                    alt="ดูข้อมูล"
                    title="ดูข้อมูล"
                    class="btn-icon btn-sm"
                    @click="
                      $router.push({
                        name: 'fix-view',
                        params: { id: row.item.id },
                      })
                    "
                  >
                    <feather-icon icon="EyeIcon" style="margin-bottom: -2px" />
                  </b-button>
                  <!-- <b-button
                    variant="outline-success"
                    alt="แก้ไข"
                    title="แก้ไข"
                    class="btn-icon btn-sm"
                    @click="
                      $router.push({
                        name: 'fix-edit',
                        params: { id: row.item.id },
                      })
                    "
                  >
                    <feather-icon icon="EditIcon" style="margin-bottom: -2px" />
                  </b-button> -->
                  <!-- <b-button
                    @click="onConfirmDelete(row.item.id)"
                    variant="outline-danger"
                    alt="ลบ"
                    title="ลบ"
                    class="btn-icon btn-sm"
                  >
                    <feather-icon
                      icon="TrashIcon"
                      style="margin-bottom: -2px"
                    />
                  </b-button> -->
                </template>
              </b-table>
            </b-col>
          </b-row>

          <b-row>
            <b-col cols="12" class="text-center">
              <b-pagination
                v-model="currentPage"
                :total-rows="totalItems"
                :per-page="perPage.code"
                align="center"
                aria-controls="my-mou"
                @change="onChangePage"
              />
            </b-col>
          </b-row>
        </div>
      </b-overlay>
    </b-card>
  </div>
</template>
