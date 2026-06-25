import api from "../services/api";
import { useGuestId } from "./useGuestId";

export function useReaction() {
    const { guestId } = useGuestId();

    const react = (post, reactionType) => {
        return api.post(`/posts/${post.id}/reactions`, {
            reaction_type: reactionType,
            guest_identifier: guestId ?? null,
        });
    };

    return { react };
}
