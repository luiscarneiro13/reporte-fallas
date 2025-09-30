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

export async function store(insert) {
    return axios
        .post("/api/admin-sucursal/cliente/store", {
            branch_id: insert.branch_id,
            email: insert.email ?? null,
            phone: insert.phone ?? null,
            cedula: insert.cedula,
            address: insert.address ?? null,
            name: insert.name,
        })
        .then(async (response) => {
            return response.data;
        })
        .catch((error) => {
            console.log(error);
        });
}

