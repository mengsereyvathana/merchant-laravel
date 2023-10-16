export const handleErrorMsg = (errorCode: string) => {
    switch (errorCode) {
        case "auth/invalid-verification-id":
            return { errorMsg: "Invalid OTP" };
        case "auth/invalid-verification-code":
            return { errorMsg: "Invalid OTP" };
        case "auth/too-many-requests":
            return { errorMsg: "Too many request. Try again later." };
        default:
            return { errorMsg: "Something went wrong." };
    }
};
