export const handleErrorMsg = (errorCode: string) => {
    switch (errorCode) {
        case "auth/invalid-verification-id":
            return { errorMsg: "Invalid OTP" };
        case "auth/invalid-verification-code":
            return { errorMsg: "Invalid OTP" };
        default:
            return { errorMsg: "Something went wrong." };
    }
};
