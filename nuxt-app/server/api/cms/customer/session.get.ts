import { fetchCustomerProfile } from "~~/server/features/auth/customer-auth";

export default defineEventHandler(async (event) => {
  return {
    data: await fetchCustomerProfile(event),
  };
});
