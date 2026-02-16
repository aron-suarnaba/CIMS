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

    // Convert timestamp to yyyy-MM-dd format for HTML5 date input
    const formatDateForInput = (dateString) => {
        if (!dateString) return '';

        try {
            const date = new Date(dateString);

            if (isNaN(date.getTime())) {
                return '';
            }

            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        } catch (error) {
            return '';
        }
    };

    return {
        formatDate,
        formatDateForInput,
    };
}
