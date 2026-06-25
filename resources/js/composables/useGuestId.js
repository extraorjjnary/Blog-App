import { useAuthStore } from "../stores/AuthStore";

const generateId = () => {
    return "guest_" + Math.random().toString(36).substring(2, 11);
};

export function useGuestId() {
    const auth = useAuthStore();

    let guestId = localStorage.getItem("guest_id");

    if (auth.isLoggedIn) {
        localStorage.removeItem("guest_id");
        guestId = null;
    } else if (!guestId) {
        guestId = generateId();
        localStorage.setItem("guest_id", guestId);
    }

    return { guestId };
}
