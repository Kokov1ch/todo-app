import React, {useEffect} from 'react';
import {toast, ToastContainer} from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import {useDispatch, useSelector} from "react-redux";

const SimpleToast = () => {

    return (
        <ToastContainer
            position='bottom-right'
            autoClose={10000}
            hideProgressBar={false}
            newestOnTop={false}
            closeOnClick
            rtl={false}
            pauseOnFocusLoss
            draggable
            pauseOnHover
            limit={5}
            theme='dark'
        />
    );
}

export default SimpleToast;
export const notify = (message) => toast(message);
export const notifyWarn = (message) => toast.warn(message);
export const notifyError = (message) => toast.error(message);
export const notifySuccess = (message) => toast.success(message);