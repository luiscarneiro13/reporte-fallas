import axios from "axios";

export async function index(event) {
    return axios
        .get("/api/admin-sucursal/cliente/index?query=" + event)
        .then(async (response) => {
            return await response.data;
        })
        .catch((error) => {
            console.log(error);
        });
}

export async function store(url, inserts) {
    return axios
        .post(url, inserts)
        .then(async (response) => {
            return response.data;
        })
        .catch((error) => {
            console.log(error);
        });
}

