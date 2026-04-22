import { fetchCustomerProfile } from "~~/server/utils/customer-auth";

export default defineEventHandler(async (event) => {
  return {
    data: await fetchCustomerProfile(event),
  };
});
