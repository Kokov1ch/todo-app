import axios, { AxiosRequestConfig, CancelTokenSource, AxiosResponse } from "axios";
import CONFIG from "../../config";
import router from '../../polyfills/router';
import { getJWT, setJWT, getRefreshToken, setRefreshToken, removeJWT, removeRefreshToken } from '../services/JWT/jwtService';
import { notifyError, notifySuccess, notifyWarn } from "../../components/Toast/SimpleToast";

let cancelToken: CancelTokenSource | undefined;

export interface AuthorizationResponse {
    data?: any;
    error?: any;
}

export function authorization(data: any): Promise<AuthorizationResponse> {
    return axios({
        method: 'POST',
        url: CONFIG.API_BASE_URL + '/login_check',
        data: data
    })
        .then(res => {
            return { data: res.data };
        })
        .catch(err => {
            if (err.response.data.code === 401) {
                notifyWarn("Invalid user's data");
            } else if (err.response.data.code === 500) {
                notifyError(err.response.data.message);
            }

            return { error: err.response.data };
        });
}

export function refreshingToken(): Promise<AuthorizationResponse> | undefined {
    if (cancelToken) {
        return;
    }

    cancelToken = axios.CancelToken.source();
    const refreshToken = getRefreshToken();

    return axios({
        method: 'POST',
        url: CONFIG.API_BASE_URL + '/token/refresh',
        data: { 'refresh_token': refreshToken },
        cancelToken: cancelToken.token
    })
        .then(res => {
            setJWT(res.data.token);
            setRefreshToken(res.data.refresh_token);
            cancelToken = undefined;
            return { data: res.data.code };
        })
        .catch(err => {
            cancelToken = undefined;
            if (err.response.data.code === 401) {
                removeJWT();
                removeRefreshToken();
                router.push('/');
            }
            return { error: err.response.data };
        });
}

export function getRequest(path: string, headers: AxiosRequestConfig['headers'] = {}): Promise<AuthorizationResponse> {
    const token = getJWT();
    return axios({
        method: 'GET',
        url: CONFIG.API_BASE_URL + path,
        headers: {
            Authorization: `Bearer ${token}`,
            ...headers
        }
    })
        .then(res => {
            return { data: res.data };
        })
        .catch(err => {
            if (err.response.data.code === 401) {
                refreshingToken();
                return getRequest(path, headers);
            } else {
                notifyError(err.response.data.errors);
            }

            return { error: err.response.data };
        });
}

export function postRequest(path: string, data: any = {}, headers: AxiosRequestConfig['headers'] = {}): Promise<AuthorizationResponse> {
    const token = getJWT();
    return axios({
        method: 'POST',
        url: CONFIG.API_BASE_URL + path,
        headers: {
            Authorization: `Bearer ${token}`,
            ...headers
        },
        data: data
    })
        .then(res => {
            notifySuccess(res.data.success);
            return { data: res.data };
        })
        .catch(err => {
            if (err.response.data.code === 401) {
                refreshingToken();
                return postRequest(path, data, headers);
            } else {
                notifyError(err.response.data.errors);
            }

            return { error: err.response.data };
        });
}

export function getRequestMocky(path: string, headers: AxiosRequestConfig['headers'] = {}): Promise<AxiosResponse> {
    return axios({
        method: 'GET',
        url: path,
        headers: headers
    });
}

export function postRequestMocky(path: string, data: any = {}, headers: AxiosRequestConfig['headers'] = {}): Promise<AxiosResponse> {
    return axios({
        method: 'POST',
        url: path,
        headers: headers,
        data: data
    });
}

export function putRequestMocky(path: string, data: any = {}, headers: AxiosRequestConfig['headers'] = {}): Promise<AxiosResponse> {
    return axios({
        method: 'PUT',
        url: path,
        headers: headers,
        data: data
    });
}

export function deleteRequestMocky(path: string, headers: AxiosRequestConfig['headers'] = {}): Promise<AxiosResponse> {
    return axios({
        method: 'DELETE',
        url: path,
        headers: headers
    });
}