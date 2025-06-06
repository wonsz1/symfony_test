import { OpenApiAdmin } from "@api-platform/admin";

// Replace with your own API entrypoint
export const App = () => (
  <OpenApiAdmin
    entrypoint="http://localhost:8080/api"
    docEntrypoint="http://localhost:8080/api/docs.jsonopenapi"
  />
);