import axios from "axios";

export const axiosPublic = axios.create({
  baseURL: `https://apipost.innovation-develop.com/api`,
  headers: {
    "Content-Type": "application/json",
  },
});
