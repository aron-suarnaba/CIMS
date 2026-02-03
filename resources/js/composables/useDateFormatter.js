export function useDateFormatter() {
    const formatDate = (dateString, locale = 'en-US') => {
        if (!dateString) return 'N/A';

        try {
            const date = new Date(dateString);

            if (isNaN(date.getTime())) {
                return 'Invalid Date';
            }
            return new Intl.DateTimeFormat(locale, {
                year: 'numeric',
                month: 'long',
                day: '2-digit',
            }).format(date);
        } catch (error) {
            return 'N/A';
        }
    };

    return {
        formatDate,
    };
}
