import { List, Datagrid, TextField } from 'react-admin';

export const CategoryList = () => {
    return (
        <List>
            <Datagrid rowClick="edit">
                <TextField source="id" />
                <TextField source="name" />
            </Datagrid>
        </List>
    );
};
