import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import Cookies from 'universal-cookie';
import {authorization} from "../../api";

type DataType = {
    token: string,
    refresh_token: string
}

type AuthState = {
    authInfo: {
        data: DataType | any,
        isLoading: boolean,
        error: string | undefined
    }
}

const initialState: AuthState = {
    authInfo: {
        data: null,
        isLoading: false,
        error: undefined
    }
};

interface AuthRes {
    refresh_token: string,
    token: string
}

interface ErrRes {
    class: string
    detail: string
    status: number
    title: string
    trace: []
    type: string
}

interface DataReq {
    password: string,
    username: string
}

export const getToken = createAsyncThunk<Promise<{ data: AuthRes } | { error: ErrRes }>, DataReq>(
    '/authentication/login_check',
    // @ts-ignore
    async (data) => authorization(data)
);

export const authSlice = createSlice({
    name: 'authentication',
    initialState,
    reducers: {
        saveToken: (state) => {
            const cookies = new Cookies();
            cookies.set('JWT-Token', state.authInfo.data.token, {path: '/'});
            cookies.set('refresh_token', state.authInfo.data.refresh_token, {path: '/'});
        }
    },
    extraReducers: (builder) => {
        builder
            .addCase(getToken.pending, (state) => {
                state.authInfo.isLoading = true;
            })
            .addCase(getToken.fulfilled, (state, action) => {
                state.authInfo = {...state.authInfo, ...action.payload, isLoading: false}
            })
            .addCase(getToken.rejected, (state, action) => {
                state.authInfo.isLoading = false;
                state.authInfo.error = action.error.message;
            })
    }
});

export const {
    saveToken
} = authSlice.actions;

export default authSlice.reducer;