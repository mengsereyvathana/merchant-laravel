import { AxiosResponse } from "axios"

class HandleResponse {
    axiosResponse(response: AxiosResponse) {
        if (response.status === 200) {
            const data = response.data
            if (!data) {
                console.log('API Error. No data!')
            }
            return data
        }
        console.log('API Error! Invalid status code!')
    }
}

export const handleResponse = new HandleResponse()