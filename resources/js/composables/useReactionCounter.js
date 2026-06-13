import { ref, computed, reactive } from "vue";

export function useReactionCounter(post) {
    const upVoteCount = computed(
        () =>
            post.value?.reactions?.filter(
                (reaction) => reaction.reaction_type === "upvote",
            ).length ?? 0,
    );

    const downVoteCount = computed(
        () =>
            post.value?.reactions?.filter(
                (reaction) => reaction.reaction_type === "downvote",
            ).length ?? 0,
    );

    return { upVoteCount, downVoteCount };
}
