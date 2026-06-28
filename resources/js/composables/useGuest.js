import { useAuthStore } from "../stores/AuthStore";

const generateId = () => {
    return "guest_" + Math.random().toString(36).substring(2, 11);
};

const generateName = () => {
    return "guest_" + Math.random().toString(36).substring(2, 6).toUpperCase();
};

export function useGuest() {
    const auth = useAuthStore();

    // for generating random guest identifier
    let guestId = localStorage.getItem("guest_id");

    if (auth.isLoggedIn) {
        localStorage.removeItem("guest_id");
        guestId = null;
    } else if (!guestId) {
        guestId = generateId();
        localStorage.setItem("guest_id", guestId);
    }

    // for generating random guest name

    let guestName = localStorage.getItem("guest_name");
    if (auth.isLoggedIn) {
        localStorage.removeItem("guest_name");
        guestName = null;
    } else if (!guestName) {
        guestName = generateName();
        localStorage.setItem("guest_name", guestName);
    }

    return { guestId, guestName };
}
