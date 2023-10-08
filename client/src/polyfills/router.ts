import {useLocation, useParams} from "react-router-dom";

// @deprecated use useRouter() hook instead
const router = {
    back: () => history.back(), // TODO: import { goBack } from "redux-first-history";
    push: (path: string) => { // TODO: use import { push } from "redux-first-history";
        window.location.replace(path);
    },
};

export const useRouter = () => {
    const query = useParams();
    const pathname = useLocation().pathname;

    return {
        ...router,
        query,
        pathname,
    };
}

export default router;